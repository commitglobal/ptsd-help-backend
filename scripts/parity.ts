/**
 * Compares the Strapi /api/app/* responses with the legacy S3 JSON files.
 * Requires a running Strapi (npm run develop) with imported content.
 *
 *   npm run parity
 */
import dns from 'node:dns';
import fs from 'node:fs';
import path from 'node:path';
import { applyLegacyFixes } from './lib/legacy-fixes';
import { canonicalizeRichText } from './lib/canonical-rich-text';
import { firstDifference, normalize } from './lib/normalize';

// Node 18 resolves "localhost" to ::1 first and does not fall back to IPv4, while Strapi listens on 0.0.0.0.
dns.setDefaultResultOrder('ipv4first');

const CMS_URL = (process.env.CMS_URL ?? 'http://localhost:1337').replace(/\/$/, '');
const S3_DIR = path.resolve(process.env.S3_CONTENT_DIR ?? path.join(__dirname, '..', '..', 's3-content'));
const MEDIA_MAP_FILE = path.join(__dirname, '.media-map.json');
const TARGETS: Record<string, string[]> = { AM: ['hy'], RO: ['ro', 'en'] };

type Case = { name: string; endpoint: string; file: string };

const buildCases = (): Case[] => {
  const cases: Case[] = [];
  for (const [country, locales] of Object.entries(TARGETS)) {
    cases.push({ name: `foggles ${country}`, endpoint: `/api/app/config/foggles/${country}`, file: `config/foggles_${country}.json` });
    for (const locale of locales) {
      cases.push({
        name: `tools-assets ${country}/${locale}`,
        endpoint: `/api/app/config/tools-assets/${country}/${locale}`,
        file: `config/CMSToolsAssetsMapping_${country}_${locale}.json`,
      });
      for (const kind of ['learn', 'support']) {
        cases.push({
          name: `${kind} ${country}/${locale}`,
          endpoint: `/api/app/content/${country}/${locale}/${kind}`,
          file: `content/resources/${country}/${locale}/${kind}.json`,
        });
      }
    }
  }
  return cases;
};

async function main() {
  const mediaMap: Record<string, { url: string }> = JSON.parse(fs.readFileSync(MEDIA_MAP_FILE, 'utf8'));
  const originalByNewUrl = new Map<string, string>();
  for (const [original, { url }] of Object.entries(mediaMap)) {
    originalByNewUrl.set(url, original);
    originalByNewUrl.set(`${CMS_URL}${url}`, original);
  }
  const resolveActual = (url: string) => originalByNewUrl.get(url) ?? url;

  let failures = 0;

  for (const c of buildCases()) {
    const res = await fetch(`${CMS_URL}${c.endpoint}`);
    if (!res.ok) {
      failures++;
      console.log(`FAIL ${c.name}: HTTP ${res.status}`);
      continue;
    }
    // Compare against the importer's intended output: the same legacy-data corrections the importer applies,
    // and rich-text HTML in the canonical form produced by the blocks round trip.
    const legacy = JSON.parse(fs.readFileSync(path.join(S3_DIR, c.file), 'utf8'));
    const expected = normalize(canonicalizeRichText(applyLegacyFixes(legacy).fixed), (u) => u);
    const actual = normalize(await res.json(), resolveActual);
    const diff = firstDifference(expected, actual);
    if (diff) {
      failures++;
      console.log(`FAIL ${c.name}: ${diff}`);
    } else {
      console.log(`OK   ${c.name}`);
    }
  }

  const statusChecks: {
    name: string;
    endpoint: string;
    status?: number;
    statuses?: number[];
    method?: string;
    body?: unknown;
  }[] = [
    { name: 'unknown country/locale -> 404', endpoint: '/api/app/content/XX/zz/learn', status: 404 },
    { name: 'unknown kind -> 404', endpoint: '/api/app/content/RO/ro/other', status: 404 },
    { name: 'lowercase country -> 200', endpoint: '/api/app/content/ro/ro/learn', status: 200 },
    { name: 'standard REST closed -> 404', endpoint: '/api/topics', status: 404 },
    {
      name: 'public register closed',
      endpoint: '/api/auth/local/register',
      method: 'POST',
      body: { username: 'x', email: 'x@example.com', password: 'Password123!' },
      statuses: [401, 403, 404],
    },
  ];
  for (const check of statusChecks) {
    const res = await fetch(`${CMS_URL}${check.endpoint}`, {
      method: check.method ?? 'GET',
      headers: check.body ? { 'Content-Type': 'application/json' } : undefined,
      body: check.body ? JSON.stringify(check.body) : undefined,
    });
    const accepted = check.statuses ?? (check.status !== undefined ? [check.status] : []);
    if (accepted.includes(res.status)) {
      console.log(`OK   ${check.name}`);
    } else {
      failures++;
      console.log(`FAIL ${check.name}: HTTP ${res.status}`);
    }
  }

  console.log(failures ? `\n${failures} check(s) failed` : '\nAll checks passed');
  process.exit(failures ? 1 : 0);
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
