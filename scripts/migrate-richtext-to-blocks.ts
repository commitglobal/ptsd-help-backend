/**
 * One-off, idempotent migration of rich-text HTML (CKEditor `content`) to Strapi blocks (`body`).
 *
 *   npm run build && npx tsx scripts/migrate-richtext-to-blocks.ts   # with the Strapi server stopped
 *
 * For every topic in every locale:
 *   - `section.rich-text` with an empty `body` and a non-empty `content` -> body = htmlToBlocks(content)
 *   - `shared.block` of type rich-text (inside multi-content / multi-page), same rule.
 * Before anything is written, the HTML of every affected item is saved to scripts/.richtext-backup.json.
 * Before migrating, a throwaway topic is written and read back to check that Strapi keeps the custom
 * paragraph property `tag: 'p'` unchanged; the migration aborts if it does not.
 */
import fs from 'node:fs';
import path from 'node:path';
import { compileStrapi, createStrapi, type Core } from '@strapi/strapi';
import { htmlToBlocks, type Blocks } from '../src/lib/rich-text';
import { SECTIONS_POPULATE } from '../src/api/app/populate';

const TOPIC_UID = 'api::topic.topic';
const BACKUP_FILE = path.join(__dirname, '.richtext-backup.json');
const PAGE_SIZE = 100;

type Json = Record<string, any>;
type BackupEntry = { documentId: string; locale: string; uid: string; path: string; content: string };

const isEmptyBody = (body: unknown) => !Array.isArray(body) || body.length === 0;
const hasHtml = (content: unknown): content is string => typeof content === 'string' && content.trim() !== '';
const mediaId = (media: Json | null | undefined) => (media && typeof media === 'object' ? media.id : media ?? null);

/** Copies a component for writing: drops its row id, passes media as ids. */
const action = (a: Json | null | undefined) => {
  if (!a) return null;
  const { id: _id, ...rest } = a;
  return rest;
};

const block = (b: Json) => {
  const { id: _id, image, action: act, ...rest } = b;
  return { ...rest, image: mediaId(image), action: action(act) };
};

const section = (s: Json) => {
  const { id: _id, ...rest } = s;
  switch (s.__component) {
    case 'section.image':
      return { ...rest, image: mediaId(s.image) };
    case 'section.button':
      return { ...rest, action: action(s.action) };
    case 'section.multi-content':
      return { ...rest, blocks: (s.blocks ?? []).map(block) };
    case 'section.multi-page':
      return {
        ...rest,
        pages: (s.pages ?? []).map(({ id: _pid, ...page }: Json) => ({ ...page, blocks: (page.blocks ?? []).map(block) })),
      };
    default:
      return rest;
  }
};

/** Converts the rich-text items of one topic in place. Returns the backup entries of what changed. */
const convertTopic = (topic: Json): BackupEntry[] => {
  const changed: BackupEntry[] = [];
  const entry = (p: string, content: string) =>
    changed.push({ documentId: topic.documentId, locale: topic.locale, uid: topic.uid, path: p, content });

  const convertBlocks = (blocks: Json[] | null | undefined, p: string) =>
    (blocks ?? []).forEach((b, i) => {
      if (b.type === 'rich-text' && isEmptyBody(b.body) && hasHtml(b.content)) {
        entry(`${p}.blocks[${i}]`, b.content);
        b.body = htmlToBlocks(b.content);
      }
    });

  (topic.sections ?? []).forEach((s: Json, i: number) => {
    const p = `sections[${i}]`;
    if (s.__component === 'section.rich-text' && isEmptyBody(s.body) && hasHtml(s.content)) {
      entry(p, s.content);
      s.body = htmlToBlocks(s.content);
    } else if (s.__component === 'section.multi-content') {
      convertBlocks(s.blocks, p);
    } else if (s.__component === 'section.multi-page') {
      (s.pages ?? []).forEach((page: Json, pi: number) => convertBlocks(page.blocks, `${p}.pages[${pi}]`));
    }
  });
  return changed;
};

const loadTopics = async (strapi: Core.Strapi, locale: string): Promise<Json[]> => {
  const out: Json[] = [];
  for (let start = 0; ; start += PAGE_SIZE) {
    const page: Json[] = await (strapi.documents(TOPIC_UID as any) as any).findMany({
      locale,
      populate: { sections: SECTIONS_POPULATE },
      start,
      limit: PAGE_SIZE,
    });
    out.push(...page);
    if (page.length < PAGE_SIZE) return out;
  }
};

/** JSON with sorted object keys (Postgres jsonb does not keep key order). */
const stableJson = (value: unknown): string =>
  JSON.stringify(value, (_k, v) =>
    v && typeof v === 'object' && !Array.isArray(v) ? Object.fromEntries(Object.entries(v).sort(([a], [b]) => a.localeCompare(b))) : v
  );

/** Writes and reads back a throwaway topic to prove that `tag: 'p'` survives the documents API. */
const probeTagPreserved = async (strapi: Core.Strapi, locale: string, withContent: boolean) => {
  const body: Blocks = [
    { type: 'paragraph', tag: 'p', children: [{ type: 'text', text: 'probe p' }] },
    { type: 'paragraph', children: [{ type: 'text', text: 'probe div' }] },
  ];
  const docs = strapi.documents(TOPIC_UID as any) as any;
  const created = await docs.create({
    locale,
    data: {
      uid: '__blocks-probe__',
      label: 'blocks probe',
      sections: [{ __component: 'section.rich-text', body, ...(withContent ? { content: '<p>probe</p>' } : {}) }],
    },
  });
  try {
    const read = await docs.findOne({ documentId: created.documentId, locale, populate: { sections: SECTIONS_POPULATE } });
    const got = read?.sections?.[0]?.body;
    if (stableJson(got) !== stableJson(body)) {
      throw new Error(`Strapi changed the blocks value.\n  sent: ${JSON.stringify(body)}\n  got:  ${JSON.stringify(got)}`);
    }
    console.log(`Probe OK: blocks with tag "p" written and read back unchanged (${locale}).`);
  } finally {
    await docs.delete({ documentId: created.documentId, locale: '*' });
  }
};

async function main() {
  const strapi = await createStrapi(await compileStrapi()).load();
  try {
    const richTextAttrs = (strapi.components as any)['section.rich-text'].attributes;
    const blockAttrs = (strapi.components as any)['shared.block'].attributes;
    if (!richTextAttrs.body || !blockAttrs.body) throw new Error('Schema has no `body` blocks attribute yet.');
    const hasLegacyContent = Boolean(richTextAttrs.content);

    const locales: string[] = (await strapi.plugin('i18n').service('locales').find()).map((l: { code: string }) => l.code);
    if (!locales.length) throw new Error('No locales found.');

    await probeTagPreserved(strapi, locales[0], hasLegacyContent);

    const pending: { topic: Json; entries: BackupEntry[] }[] = [];
    let topicCount = 0;
    const perLocale: Record<string, number> = {};
    for (const locale of locales) {
      const topics = await loadTopics(strapi, locale);
      topicCount += topics.length;
      for (const topic of topics) {
        const entries = convertTopic(topic);
        perLocale[locale] = (perLocale[locale] ?? 0) + entries.length;
        if (entries.length) pending.push({ topic, entries });
      }
    }

    const entries = pending.flatMap((p) => p.entries);
    console.log(`Topics scanned: ${topicCount} across locales ${locales.join(', ')}`);
    console.log(`Rich-text items to migrate: ${entries.length} in ${pending.length} topic(s)`, perLocale);
    if (!entries.length) {
      console.log('Nothing to migrate.');
      return;
    }

    // Backup first. Merge with an earlier backup so a partial re-run never loses previously saved HTML.
    const previous: BackupEntry[] = fs.existsSync(BACKUP_FILE) ? JSON.parse(fs.readFileSync(BACKUP_FILE, 'utf8')) : [];
    const key = (e: BackupEntry) => `${e.documentId}|${e.locale}|${e.path}`;
    const merged = new Map(previous.map((e) => [key(e), e]));
    for (const e of entries) if (!merged.has(key(e))) merged.set(key(e), e);
    fs.writeFileSync(BACKUP_FILE, JSON.stringify([...merged.values()], null, 2));
    console.log(`Backup written: ${BACKUP_FILE} (${merged.size} item(s))`);

    const docs = strapi.documents(TOPIC_UID as any) as any;
    let written = 0;
    for (const { topic } of pending) {
      await docs.update({
        documentId: topic.documentId,
        locale: topic.locale,
        data: { sections: (topic.sections ?? []).map(section) },
      });
      written++;
    }
    console.log(`Topics updated: ${written}`);

    // Verify: every rich-text item with HTML now has a body.
    let missing = 0;
    let migrated = 0;
    for (const locale of locales) {
      for (const topic of await loadTopics(strapi, locale)) {
        const left = convertTopic(JSON.parse(JSON.stringify(topic)));
        missing += left.length;
        const count = (b: Json) => (b.type === 'rich-text' && !isEmptyBody(b.body) ? 1 : 0);
        for (const s of topic.sections ?? []) {
          if (s.__component === 'section.rich-text' && !isEmptyBody(s.body)) migrated++;
          if (s.__component === 'section.multi-content') migrated += (s.blocks ?? []).reduce((n: number, b: Json) => n + count(b), 0);
          if (s.__component === 'section.multi-page')
            for (const page of s.pages ?? []) migrated += (page.blocks ?? []).reduce((n: number, b: Json) => n + count(b), 0);
        }
      }
    }
    console.log(`Verification: rich-text items with body = ${migrated}, still without body = ${missing}`);
    if (missing) process.exitCode = 1;
  } finally {
    await waitForIdleDb(strapi);
    await strapi.destroy();
  }
}

/** Same as import-s3.ts: let fire-and-forget queries finish before destroying the knex pool. */
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
