/**
 * One-off, idempotent migration from "Tools assets" (one document per country with a repeatable
 * `shared.asset` component) to "Tool asset" (one document per country and key, file localized).
 *
 *   npm run build && npx tsx scripts/migrate-tools-assets-to-tool-asset.ts   # with the Strapi server stopped
 *
 * - Before anything is written, the database is dumped with pg_dump (custom format) to
 *   ../.superpowers/sdd/2026-09-28-strapi-cms/db-backup-before-tool-asset.dump (a timestamped file if it exists).
 * - For every old document and locale, each `{ key, file }` creates or updates the Tool asset (country, key)
 *   in that locale, reusing the same media file id (nothing is uploaded).
 * - Afterwards every old (country, locale) mapping is compared with the new entries (key -> file id).
 */
import fs from 'node:fs';
import path from 'node:path';
import { spawnSync } from 'node:child_process';
import dotenv from 'dotenv';
import { compileStrapi, createStrapi, type Core } from '@strapi/strapi';

const OLD_UID = 'api::tools-assets.tools-assets';
const NEW_UID = 'api::tool-asset.tool-asset';
const PG_DUMP = process.env.PG_DUMP ?? 'A:\\Programs\\PostgreSQL\\bin\\pg_dump.exe';
const BACKUP_DIR = path.resolve(__dirname, '..', '..', '.superpowers', 'sdd', '2026-09-28-strapi-cms');
const BACKUP_FILE = path.join(BACKUP_DIR, 'db-backup-before-tool-asset.dump');

type Json = Record<string, any>;

const backupDatabase = () => {
  dotenv.config({ path: path.resolve(__dirname, '..', '.env') });
  const env = process.env;
  if (env.DATABASE_CLIENT !== 'postgres') throw new Error('Expected DATABASE_CLIENT=postgres');
  const target = fs.existsSync(BACKUP_FILE)
    ? BACKUP_FILE.replace(/\.dump$/, `-${new Date().toISOString().replace(/[:.]/g, '-')}.dump`)
    : BACKUP_FILE;
  fs.mkdirSync(BACKUP_DIR, { recursive: true });
  const result = spawnSync(
    PG_DUMP,
    [
      '--format=custom',
      '--no-password',
      '--host', env.DATABASE_HOST ?? 'localhost',
      '--port', env.DATABASE_PORT ?? '5432',
      '--username', env.DATABASE_USERNAME ?? 'strapi',
      '--dbname', env.DATABASE_NAME ?? 'strapi',
      '--file', target,
    ],
    { env: { ...process.env, PGPASSWORD: env.DATABASE_PASSWORD ?? '' }, stdio: ['ignore', 'inherit', 'inherit'] }
  );
  if (result.status !== 0 || !fs.existsSync(target) || fs.statSync(target).size === 0) {
    throw new Error(`pg_dump failed (exit ${result.status}); nothing was written.`);
  }
  console.log(`Backup: ${target} (${fs.statSync(target).size} bytes)`);
};

const fileId = (file: Json | null | undefined) => (file && typeof file === 'object' ? file.id : file ?? null);

async function migrate(strapi: Core.Strapi) {
  if (!strapi.contentTypes[OLD_UID as keyof typeof strapi.contentTypes]) {
    console.log(`${OLD_UID} no longer exists; nothing to migrate.`);
    return;
  }
  const oldDocs: Json[] = await strapi.documents(OLD_UID as any).findMany({
    locale: '*',
    populate: { country: true, assets: { populate: { file: true } } },
  });

  const counts = { created: 0, localeAdded: 0, updated: 0, unchanged: 0, skippedNoFile: 0, repeatedKeys: 0 };
  const expected = new Map<string, Map<string, number>>(); // "RO/ro" -> key -> file id

  for (const doc of oldDocs) {
    const country = doc.country as Json | null;
    if (!country) throw new Error(`Old tools-assets ${doc.documentId}/${doc.locale} has no country; aborting.`);
    const label = `${country.code}/${doc.locale}`;
    const mapping = new Map<string, number>();
    expected.set(label, mapping);

    for (const asset of (doc.assets ?? []) as Json[]) {
      const id = fileId(asset.file);
      if (!id) {
        counts.skippedNoFile++;
        continue;
      }
      if (mapping.has(asset.key)) counts.repeatedKeys++;
      mapping.set(asset.key, id); // last one wins, like the old serializer

      const existing = await strapi.db.query(NEW_UID).findOne({
        select: ['documentId'],
        where: { key: asset.key, country: { id: country.id } },
      });
      if (!existing) {
        await strapi.documents(NEW_UID as any).create({
          locale: doc.locale,
          data: { key: asset.key, country: country.documentId, file: id },
        });
        counts.created++;
        continue;
      }
      const current: Json | null = await strapi.documents(NEW_UID as any).findOne({
        documentId: existing.documentId,
        locale: doc.locale,
        populate: { file: true },
      });
      if (current && fileId(current.file) === id) {
        counts.unchanged++;
        continue;
      }
      await strapi.documents(NEW_UID as any).update({
        documentId: existing.documentId,
        locale: doc.locale,
        data: { key: asset.key, country: country.documentId, file: id },
      });
      if (current) counts.updated++;
      else counts.localeAdded++;
    }
  }

  console.log('Writes:', counts);

  // Verify: every old (country, locale) mapping is served identically by the new entries.
  let mismatches = 0;
  for (const [label, mapping] of expected) {
    const [code, locale] = label.split('/');
    const rows: Json[] = await strapi.documents(NEW_UID as any).findMany({
      locale,
      filters: { country: { code } },
      populate: { file: true },
    });
    const actual = new Map(rows.map((r) => [r.key as string, fileId(r.file)]));
    for (const [key, id] of mapping) {
      if (actual.get(key) !== id) {
        mismatches++;
        console.log(`MISMATCH ${label} ${key}: expected file ${id}, got ${actual.get(key)}`);
      }
    }
    const extra = [...actual.keys()].filter((k) => !mapping.has(k));
    if (extra.length) console.log(`NOTE ${label}: ${extra.length} Tool asset(s) not in the old entry: ${extra.join(', ')}`);
    console.log(`${label}: old ${mapping.size} keys, new ${rows.length} entries`);
  }

  const all: Json[] = await strapi.documents(NEW_UID as any).findMany({ locale: '*', populate: { country: true } });
  const perCountry = new Map<string, { docs: Set<string>; locales: Set<string>; rows: number }>();
  for (const row of all) {
    const code = row.country?.code ?? '(none)';
    const entry = perCountry.get(code) ?? { docs: new Set(), locales: new Set(), rows: 0 };
    entry.docs.add(row.documentId);
    entry.locales.add(row.locale);
    entry.rows++;
    perCountry.set(code, entry);
  }
  for (const [code, { docs, locales, rows }] of perCountry) {
    console.log(`${code}: ${docs.size} documents x ${locales.size} locale(s) [${[...locales].join(', ')}] = ${rows} rows`);
  }

  if (mismatches) throw new Error(`${mismatches} mismatch(es) between old and new tool assets`);
  console.log('Verification passed: every old key maps to the same file id.');
}

async function main() {
  backupDatabase();
  const strapi = await createStrapi(await compileStrapi()).load();
  try {
    await migrate(strapi);
  } finally {
    await waitForIdleDb(strapi);
    await strapi.destroy();
  }
}

async function waitForIdleDb(strapi: Core.Strapi, timeoutMs = 15000) {
  const pool = (strapi.db.connection as any).client?.pool;
  if (!pool) return;
  const deadline = Date.now() + timeoutMs;
  const busy = () => pool.numUsed() + pool.numPendingAcquires() + pool.numPendingCreates() > 0;
  while (busy() && Date.now() < deadline) await new Promise((r) => setTimeout(r, 100));
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
