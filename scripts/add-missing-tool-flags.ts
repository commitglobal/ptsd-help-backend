/**
 * One-off, idempotent: adds the app tools (and their subcategories) that are missing from each country's
 * Tool visibility entry, with `enabled: true`. Existing flags keep their values and order.
 *
 *   npx tsx scripts/add-missing-tool-flags.ts
 *
 * Uses the already built `dist` (run `npm run build` or `npm run develop` first, after `npm run generate:keys`).
 * Before anything is written, the current flags of every entry are saved to scripts/.feature-flags-backup.json.
 */
import fs from 'node:fs';
import path from 'node:path';
import { createStrapi, type Core } from '@strapi/strapi';
import { FEATURE_FLAGS_POPULATE } from '../src/api/app/populate';

const FEATURE_FLAG_UID = 'api::feature-flag.feature-flag';
const BACKUP_FILE = path.join(__dirname, '.feature-flags-backup.json');

/** Every tool of the mobile app (hooks/useTools.ts TOOLS_CONFIG) with its subcategories. */
const APP_TOOLS: Record<string, string[]> = {
  'observe-thoughts': ['clouds', 'river'],
  'postive-imagery': ['beach', 'country-road', 'forest'],
  'body-scan': ['julia', 'robyn'],
  'muscle-relaxation': [],
  'deep-breathing': [],
  relationships: [
    'relationships-reconnect-with-partner',
    'relationships-positive-communication',
    'relationships-i-messages',
    'relationships-healthy-arguments',
  ],
  'ambient-sounds': [],
  mindfulness: [
    'mindfulness-conscious-breathing',
    'mindfulness-mindful-walking',
    'mindfulness-emotional-discomfort',
    'mindfulness-sense-awareness',
    'mindfulness-loving-kindness',
  ],
  pause: [],
  'my-feelings': [],
  sleep: ['sleep-help', 'sleep-habits', 'sleep-perspective'],
  'worry-time': [],
  rid: [],
  'soothe-senses': [],
  'connect-with-others': [],
  'change-perspective': [],
  grounding: [],
  quotes: [],
  'my-strengths': [],
  'shift-thoughts': [],
  'recreational-activities': [
    'recreational-activities-alone',
    'recreational-activities-city',
    'recreational-activities-nature',
  ],
};

type SubFlag = { key: string; enabled: boolean };
type ToolFlag = { key: string; enabled: boolean; subcategories: SubFlag[] };

/** Copies a stored flag for writing (component row ids dropped) and appends missing subcategories. */
const completeTool = (tool: ToolFlag, added: string[]): ToolFlag => {
  const subcategories = (tool.subcategories ?? []).map(({ key, enabled }) => ({ key, enabled }));
  for (const key of APP_TOOLS[tool.key] ?? []) {
    if (!subcategories.some((s) => s.key === key)) {
      subcategories.push({ key, enabled: true });
      added.push(`${tool.key}/${key}`);
    }
  }
  return { key: tool.key, enabled: tool.enabled, subcategories };
};

async function main() {
  const strapi = await createStrapi({ appDir: process.cwd(), distDir: path.join(process.cwd(), 'dist') }).load();
  try {
    const docs = strapi.documents(FEATURE_FLAG_UID as any);
    const entries = (await docs.findMany({ populate: { country: true, ...FEATURE_FLAGS_POPULATE } as any })) as any[];

    fs.writeFileSync(
      BACKUP_FILE,
      JSON.stringify(entries.map((e) => ({ documentId: e.documentId, country: e.country?.code, tools: e.tools })), null, 2)
    );
    console.log(`backup: ${path.relative(process.cwd(), BACKUP_FILE)} (${entries.length} entries)`);

    for (const entry of entries) {
      const added: string[] = [];
      const tools = (entry.tools as ToolFlag[]).map((tool) => completeTool(tool, added));
      for (const key of Object.keys(APP_TOOLS)) {
        if (!tools.some((t) => t.key === key)) {
          tools.push(completeTool({ key, enabled: true, subcategories: [] }, []));
          added.push(key);
        }
      }

      const country = entry.country?.code ?? entry.documentId;
      if (added.length === 0) {
        console.log(`${country}: nothing to add`);
        continue;
      }
      await docs.update({ documentId: entry.documentId, data: { tools } as any });
      console.log(`${country}: added ${added.join(', ')}`);
    }
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
