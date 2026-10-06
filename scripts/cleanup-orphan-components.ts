/**
 * One-off, idempotent cleanup of orphan component rows: rows in component tables that no live parent
 * references (left behind when Strapi 5.11 replaced components during updates).
 *
 *   npx tsx scripts/cleanup-orphan-components.ts            # dry run: prints counts, writes the id lists
 *   npx tsx scripts/cleanup-orphan-components.ts --apply    # deletes, in one transaction
 *
 * Run with the Strapi server stopped.
 *
 * Liveness is computed top-down from the content-type rows (every row of a content type is live):
 * a component row is live when a `*_cmps` row whose parent (entity_id) is live references it
 * (cmp_id + component_type). The walk repeats until nothing changes, so nested trees
 * (multi-page -> page -> block -> action, button -> action, tool-flag -> subtool-flag, ...) are covered.
 *
 * --apply, in one transaction:
 *   - deletes the `*_cmps` rows owned by orphan rows (entity_id) or pointing at them (cmp_id),
 *   - deletes `files_related_mph` rows whose related_type/related_id point at orphan rows,
 *   - deletes the orphan rows, deepest components first (relation link tables cascade via FK),
 *   - re-counts live content and rolls back if anything changed.
 */
import fs from 'node:fs';
import path from 'node:path';
import { compileStrapi, createStrapi, type Core } from '@strapi/strapi';

const APPLY = process.argv.includes('--apply');
const ORPHANS_FILE =
  process.env.ORPHANS_FILE ?? path.resolve(__dirname, '..', '..', '.superpowers', 'sdd', '2026-09-28-strapi-cms', 'orphans.json');

type Json = Record<string, any>;
type Node = { uid: string; table: string; cmpsTable: string | null; isRoot: boolean; children: string[] };

/** Builds the component tree from the loaded schema: roots are api:: content types. */
const buildNodes = async (strapi: Core.Strapi) => {
  const knex = strapi.db.connection;
  const nodes = new Map<string, Node>();
  const add = async (uid: string, schema: Json, isRoot: boolean) => {
    const children = new Set<string>();
    for (const attr of Object.values<Json>(schema.attributes ?? {})) {
      if (attr.type === 'component') children.add(attr.component);
      if (attr.type === 'dynamiczone') (attr.components ?? []).forEach((c: string) => children.add(c));
    }
    const table = schema.collectionName;
    const cmpsTable = children.size ? `${table}_cmps` : null;
    if (cmpsTable && !(await knex.schema.hasTable(cmpsTable))) throw new Error(`Missing join table ${cmpsTable}`);
    nodes.set(uid, { uid, table, cmpsTable, isRoot, children: [...children] });
  };
  for (const [uid, ct] of Object.entries<Json>(strapi.contentTypes as any)) if (uid.startsWith('api::')) await add(uid, ct, true);
  for (const [uid, c] of Object.entries<Json>(strapi.components as any)) await add(uid, c, false);
  return nodes;
};

/** Live ids per uid (roots: all rows). */
const computeLive = async (knex: any, nodes: Map<string, Node>) => {
  const live = new Map<string, Set<number>>();
  for (const n of nodes.values()) live.set(n.uid, new Set());
  for (const n of nodes.values()) {
    if (!n.isRoot) continue;
    const rows: { id: number }[] = await knex(n.table).select('id');
    rows.forEach((r) => live.get(n.uid)!.add(r.id));
  }
  // Fixed point: pull children referenced from live parents.
  for (let changed = true; changed; ) {
    changed = false;
    for (const n of nodes.values()) {
      if (!n.cmpsTable) continue;
      const parents = [...live.get(n.uid)!];
      if (!parents.length) continue;
      const rows: { cmp_id: number; component_type: string }[] = await knex(n.cmpsTable)
        .select('cmp_id', 'component_type')
        .whereIn('entity_id', parents);
      for (const r of rows) {
        const set = live.get(r.component_type);
        if (!set) throw new Error(`${n.cmpsTable} references unknown component ${r.component_type}`);
        if (!set.has(r.cmp_id)) {
          set.add(r.cmp_id);
          changed = true;
        }
      }
    }
  }
  return live;
};

const computeOrphans = async (knex: any, nodes: Map<string, Node>) => {
  const live = await computeLive(knex, nodes);
  const orphans = new Map<string, number[]>();
  const totals = new Map<string, number>();
  for (const n of nodes.values()) {
    if (n.isRoot) continue;
    const rows: { id: number }[] = await knex(n.table).select('id').orderBy('id');
    totals.set(n.uid, rows.length);
    orphans.set(n.uid, rows.map((r) => r.id).filter((id) => !live.get(n.uid)!.has(id)));
  }
  return { live, orphans, totals };
};

/** Children before parents. */
const deepestFirst = (nodes: Map<string, Node>) => {
  const out: string[] = [];
  const seen = new Set<string>();
  const visit = (uid: string) => {
    if (seen.has(uid)) return;
    seen.add(uid);
    nodes.get(uid)!.children.forEach(visit);
    out.push(uid);
  };
  [...nodes.keys()].forEach(visit);
  return out.filter((uid) => !nodes.get(uid)!.isRoot);
};

/** Counts of live content that must not change. */
const liveCounts = async (knex: any, nodes: Map<string, Node>) => {
  const counts: Record<string, number> = {};
  const topics: { locale: string; n: string }[] = await knex('topics').select('locale').count({ n: '*' }).groupBy('locale');
  topics.forEach((r) => (counts[`topics ${r.locale}`] = Number(r.n)));
  const sections: { locale: string; component_type: string; n: string }[] = await knex('topics_cmps as c')
    .join('topics as t', 't.id', 'c.entity_id')
    .select('t.locale', 'c.component_type')
    .count({ n: '*' })
    .groupBy('t.locale', 'c.component_type');
  sections.forEach((r) => (counts[`sections ${r.locale} ${r.component_type}`] = Number(r.n)));
  const { live } = { live: await computeLive(knex, nodes) };
  for (const [uid, ids] of live) if (!nodes.get(uid)!.isRoot) counts[`live ${uid}`] = ids.size;
  return counts;
};

const chunk = <T>(xs: T[], size = 1000) => Array.from({ length: Math.ceil(xs.length / size) }, (_, i) => xs.slice(i * size, i * size + size));

async function main() {
  const strapi = await createStrapi(await compileStrapi()).load();
  try {
    const knex = strapi.db.connection as any;
    const nodes = await buildNodes(strapi);
    const cmpsTables = [...nodes.values()].filter((n) => n.cmpsTable).map((n) => n.cmpsTable!);

    const { orphans, totals } = await computeOrphans(knex, nodes);
    console.log(`Mode: ${APPLY ? 'APPLY' : 'dry run'}`);
    console.log('Orphan rows per table (orphans / total):');
    for (const uid of deepestFirst(nodes)) {
      const n = nodes.get(uid)!;
      console.log(`  ${n.table.padEnd(36)} ${uid.padEnd(22)} ${String(orphans.get(uid)!.length).padStart(5)} / ${totals.get(uid)}`);
    }
    const orphanCount = [...orphans.values()].reduce((a, ids) => a + ids.length, 0);

    // Dangling links that go with the orphan rows.
    const linkRows = new Set<string>(); // `${table}:${id}`, so a row that is both owned by and points at an orphan counts once
    for (const t of cmpsTables) {
      for (const [uid, ids] of orphans) {
        for (const part of chunk(ids)) {
          const rows: { id: number }[] = await knex(t).select('id').where('component_type', uid).whereIn('cmp_id', part);
          rows.forEach((r) => linkRows.add(`${t}:${r.id}`));
        }
      }
      const owner = [...nodes.values()].find((n) => n.cmpsTable === t)!;
      for (const part of chunk(orphans.get(owner.uid) ?? [])) {
        const rows: { id: number }[] = await knex(t).select('id').whereIn('entity_id', part);
        rows.forEach((r) => linkRows.add(`${t}:${r.id}`));
      }
    }
    const cmpsLinks = linkRows.size;
    let mediaLinks = 0;
    for (const [uid, ids] of orphans)
      for (const part of chunk(ids)) {
        const [{ n }] = await knex('files_related_mph').where('related_type', uid).whereIn('related_id', part).count({ n: '*' });
        mediaLinks += Number(n);
      }
    console.log(`Total orphan rows: ${orphanCount}; *_cmps rows linked to them: ${cmpsLinks}; files_related_mph rows: ${mediaLinks}`);

    const dump = Object.fromEntries(
      deepestFirst(nodes).map((uid) => [uid, { table: nodes.get(uid)!.table, total: totals.get(uid), orphanIds: orphans.get(uid) }])
    );
    if (orphanCount) {
      fs.mkdirSync(path.dirname(ORPHANS_FILE), { recursive: true });
      fs.writeFileSync(ORPHANS_FILE, JSON.stringify({ generatedAt: new Date().toISOString(), mode: APPLY ? 'apply' : 'dry-run', orphans: dump }, null, 2));
      console.log(`Orphan id lists written: ${ORPHANS_FILE}`);
    } else {
      console.log(`No orphans; ${ORPHANS_FILE} left as is.`);
    }

    if (!APPLY) return;
    if (!orphanCount) {
      console.log('Nothing to delete.');
      return;
    }

    const before = await liveCounts(knex, nodes);
    const deleted: Record<string, number> = {};
    await knex.transaction(async (trx: any) => {
      let cmpsDeleted = 0;
      let mediaDeleted = 0;
      for (const [uid, ids] of orphans) {
        if (!ids.length) continue;
        for (const part of chunk(ids)) {
          for (const t of cmpsTables) cmpsDeleted += await trx(t).where('component_type', uid).whereIn('cmp_id', part).del();
          const own = nodes.get(uid)!.cmpsTable;
          if (own) cmpsDeleted += await trx(own).whereIn('entity_id', part).del();
          mediaDeleted += await trx('files_related_mph').where('related_type', uid).whereIn('related_id', part).del();
        }
      }
      for (const uid of deepestFirst(nodes)) {
        const ids = orphans.get(uid)!;
        let n = 0;
        for (const part of chunk(ids)) n += await trx(nodes.get(uid)!.table).whereIn('id', part).del();
        if (n !== ids.length) throw new Error(`${uid}: expected to delete ${ids.length}, deleted ${n}`);
        deleted[nodes.get(uid)!.table] = n;
      }
      console.log(`Deleted: component rows ${Object.values(deleted).reduce((a, b) => a + b, 0)}, *_cmps rows ${cmpsDeleted}, files_related_mph rows ${mediaDeleted}`);

      const after = await liveCounts(trx, nodes);
      const diffs = Object.keys({ ...before, ...after }).filter((k) => before[k] !== after[k]);
      if (diffs.length) {
        diffs.forEach((k) => console.error(`  live count changed: ${k} ${before[k]} -> ${after[k]}`));
        throw new Error('Live content counts changed; rolling back.');
      }
      const left = await computeOrphans(trx, nodes);
      const remaining = [...left.orphans.values()].reduce((a, ids) => a + ids.length, 0);
      if (remaining) throw new Error(`${remaining} orphan row(s) still present; rolling back.`);
      console.log(`Live content counts unchanged (${Object.keys(before).length} counters). Orphans left: 0. Committing.`);
    });
    console.log('Deleted per table:', deleted);
  } finally {
    await strapi.destroy();
  }
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
