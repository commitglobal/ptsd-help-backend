/**
 * One-off, idempotent cleanup: clears the leftover legacy HTML in `shared.block.content`
 * for rich-text blocks that already have a non-empty `body` (Strapi blocks).
 *
 *   npx tsx scripts/cleanup-legacy-richtext-content.ts   # with the Strapi server stopped
 *
 * Only blocks with `type: 'rich-text'`, a non-empty `body` and a non-empty `content` are touched.
 * Blocks with an empty `body` are left alone. Each affected topic/locale is updated through the
 * documents API with its full sections array (media passed as ids). Afterwards the data is
 * re-read to check that no rich-text block keeps both fields and that every `body` is unchanged.
 */
import { compileStrapi, createStrapi, type Core } from '@strapi/strapi';
import { SECTIONS_POPULATE } from '../src/api/app/populate';

const TOPIC_UID = 'api::topic.topic';
const PAGE_SIZE = 100;

type Json = Record<string, any>;

const hasBody = (body: unknown) => Array.isArray(body) && body.length > 0;
const hasContent = (content: unknown) => typeof content === 'string' && content.trim() !== '';
const mediaId = (media: Json | null | undefined) => (media && typeof media === 'object' ? media.id : media ?? null);
const needsCleanup = (b: Json) => b.type === 'rich-text' && hasBody(b.body) && hasContent(b.content);

/** JSON with sorted object keys (Postgres jsonb does not keep key order). */
const stableJson = (value: unknown): string =>
  JSON.stringify(value, (_k, v) =>
    v && typeof v === 'object' && !Array.isArray(v) ? Object.fromEntries(Object.entries(v).sort(([a], [b]) => a.localeCompare(b))) : v
  );

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

/** Every shared.block of a topic, with a stable path. */
const blocksOf = (topic: Json): { path: string; block: Json }[] => {
  const out: { path: string; block: Json }[] = [];
  (topic.sections ?? []).forEach((s: Json, i: number) => {
    if (s.__component === 'section.multi-content')
      (s.blocks ?? []).forEach((b: Json, bi: number) => out.push({ path: `sections[${i}].blocks[${bi}]`, block: b }));
    if (s.__component === 'section.multi-page')
      (s.pages ?? []).forEach((p: Json, pi: number) =>
        (p.blocks ?? []).forEach((b: Json, bi: number) => out.push({ path: `sections[${i}].pages[${pi}].blocks[${bi}]`, block: b }))
      );
  });
  return out;
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

async function main() {
  const strapi = await createStrapi(await compileStrapi()).load();
  try {
    const locales: string[] = (await strapi.plugin('i18n').service('locales').find()).map((l: { code: string }) => l.code);
    if (!locales.length) throw new Error('No locales found.');

    // Snapshot of every rich-text block body, keyed by documentId|locale|path.
    const bodiesBefore = new Map<string, string>();
    const pending: Json[] = [];
    const perLocale: Record<string, number> = {};
    let topicCount = 0;
    let richTextBlocks = 0;
    let emptyBodySkipped = 0;
    for (const locale of locales) {
      const topics = await loadTopics(strapi, locale);
      topicCount += topics.length;
      for (const topic of topics) {
        let changed = 0;
        for (const { path, block: b } of blocksOf(topic)) {
          if (b.type !== 'rich-text') continue;
          richTextBlocks++;
          bodiesBefore.set(`${topic.documentId}|${locale}|${path}`, stableJson(b.body ?? null));
          if (!hasBody(b.body)) {
            if (hasContent(b.content)) emptyBodySkipped++;
            continue;
          }
          if (needsCleanup(b)) {
            b.content = null;
            changed++;
          }
        }
        if (changed) {
          perLocale[locale] = (perLocale[locale] ?? 0) + changed;
          pending.push(topic);
        }
      }
    }
    const toClear = Object.values(perLocale).reduce((a, b) => a + b, 0);
    console.log(`Topics scanned: ${topicCount} across locales ${locales.join(', ')}`);
    console.log(`Rich-text blocks: ${richTextBlocks}; with body + content (to clear): ${toClear} in ${pending.length} topic(s)`, perLocale);
    console.log(`Rich-text blocks with empty body and non-empty content (left untouched): ${emptyBodySkipped}`);

    const docs = strapi.documents(TOPIC_UID as any) as any;
    let written = 0;
    for (const topic of pending) {
      await docs.update({
        documentId: topic.documentId,
        locale: topic.locale,
        data: { sections: (topic.sections ?? []).map(section) },
      });
      written++;
    }
    console.log(toClear ? `Topics updated: ${written}` : 'Nothing to clear.');

    // Verify.
    let stillBoth = 0;
    let bodyChanged = 0;
    let seen = 0;
    for (const locale of locales) {
      for (const topic of await loadTopics(strapi, locale)) {
        for (const { path, block: b } of blocksOf(topic)) {
          if (b.type !== 'rich-text') continue;
          seen++;
          if (needsCleanup(b)) stillBoth++;
          const before = bodiesBefore.get(`${topic.documentId}|${locale}|${path}`);
          if (before === undefined || before !== stableJson(b.body ?? null)) bodyChanged++;
        }
      }
    }
    console.log(
      `Verification: rich-text blocks = ${seen} (before ${richTextBlocks}), with both body and content = ${stillBoth}, body changed/missing = ${bodyChanged}`
    );
    if (stillBoth || bodyChanged || seen !== richTextBlocks) process.exitCode = 1;
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
