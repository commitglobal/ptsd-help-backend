/**
 * One-off import of the legacy S3 JSON content into Strapi.
 *
 *   npm run import:s3            # fails if content already exists
 *   npm run import:s3 -- --reset # deletes imported content types, then imports again
 *
 * Media is uploaded once and remembered in scripts/.media-map.json (original S3 URL -> Strapi file),
 * so re-imports reuse already-uploaded files.
 */
import fs from 'node:fs';
import path from 'node:path';
import mime from 'mime-types';
import { compileStrapi, createStrapi, type Core } from '@strapi/strapi';
import { applyLegacyFixes } from './lib/legacy-fixes';
import { htmlToBlocks } from '../src/lib/rich-text';

const S3_BASE = 'https://ptsdhelp-cms-test.s3.eu-central-1.amazonaws.com';
const S3_DIR = path.resolve(process.env.S3_CONTENT_DIR ?? path.join(__dirname, '..', '..', 's3-content'));
const MEDIA_MAP_FILE = path.join(__dirname, '.media-map.json');
const RESET = process.argv.includes('--reset');

const TARGETS: Record<string, string[]> = { AM: ['hy'], RO: ['ro', 'en'] };
const COUNTRY_NAMES: Record<string, string> = { AM: 'Armenia', RO: 'Romania' };
const LOCALE_NAMES: Record<string, string> = { hy: 'Armenian (hy)', ro: 'Romanian (ro)', en: 'English (en)' };
const KINDS = ['learn', 'support'] as const;

const IMPORTED_UIDS = [
  'api::content-page.content-page',
  'api::category.category',
  'api::topic.topic',
  'api::tool-asset.tool-asset',
  'api::feature-flag.feature-flag',
  'api::country.country',
] as const;

type MediaMap = Record<string, { id: number; url: string }>;
type Json = Record<string, any>;

const readJson = (rel: string): Json => JSON.parse(fs.readFileSync(path.join(S3_DIR, rel), 'utf8'));

const mapSeq = async <T, R>(items: T[], fn: (item: T) => Promise<R>): Promise<R[]> => {
  const out: R[] = [];
  for (const item of items) out.push(await fn(item));
  return out;
};

class Importer {
  private docIds = new Map<string, string>();

  constructor(
    private strapi: Core.Strapi,
    private mediaMap: MediaMap
  ) {}

  private docs(uid: string) {
    return this.strapi.documents(uid as any) as any;
  }

  async reset() {
    for (const uid of IMPORTED_UIDS) {
      const docs: { documentId: string }[] = await this.docs(uid).findMany({ locale: '*' });
      for (const documentId of new Set(docs.map((d) => d.documentId))) {
        await this.docs(uid).delete({ documentId, locale: '*' });
      }
    }
  }

  async hasContent() {
    const docs = await this.docs('api::content-page.content-page').findMany({ locale: '*', limit: 1 });
    return docs.length > 0;
  }

  async ensureLocales() {
    const service = this.strapi.plugin('i18n').service('locales');
    const existing: string[] = (await service.find()).map((l: { code: string }) => l.code);
    for (const [code, name] of Object.entries(LOCALE_NAMES)) {
      if (!existing.includes(code)) await service.create({ code, name });
    }
  }

  async media(url: string | undefined | null): Promise<number | null> {
    if (!url) return null;
    if (!url.startsWith(`${S3_BASE}/`)) throw new Error(`Media URL outside the legacy bucket: ${url}`);

    const cached = this.mediaMap[url];
    if (cached) {
      const exists = await this.strapi.db.query('plugin::upload.file').findOne({ where: { id: cached.id } });
      if (exists) return cached.id;
    }

    const filepath = path.join(S3_DIR, ...decodeURIComponent(url.slice(S3_BASE.length + 1)).split('/'));
    if (!fs.existsSync(filepath)) throw new Error(`Missing local file for ${url} (${filepath}). Run pull-s3-content.mjs first.`);

    const [file] = await this.strapi.plugin('upload').service('upload').upload({
      data: {},
      files: {
        filepath,
        originalFilename: path.basename(filepath),
        mimetype: mime.lookup(filepath) || 'application/octet-stream',
        size: fs.statSync(filepath).size,
      },
    });
    this.mediaMap[url] = { id: file.id, url: file.url };
    fs.writeFileSync(MEDIA_MAP_FILE, JSON.stringify(this.mediaMap, null, 2));
    return file.id;
  }

  /** Creates the document for the first locale, then adds further locales to the same document. */
  async upsertLocalized(uid: string, key: string, locale: string, data: Json): Promise<string> {
    const mapKey = `${uid}|${key}`;
    const documentId = this.docIds.get(mapKey);
    if (!documentId) {
      const doc = await this.docs(uid).create({ locale, data, status: 'published' });
      this.docIds.set(mapKey, doc.documentId);
      return doc.documentId;
    }
    await this.docs(uid).update({ documentId, locale, data, status: 'published' });
    return documentId;
  }

  private action(a: Json) {
    return { type: a.type, toolId: a.toolId ?? null, url: a.url ?? null, number: a.number ?? null, message: a.message ?? null };
  }

  private alt(s: Json): string {
    return s.alt ?? s.atl ?? (typeof s.content === 'string' ? s.content : '');
  }

  private async block(b: Json) {
    switch (b.type) {
      case 'image':
        return { type: 'image', image: await this.media(b.src), alt: this.alt(b) };
      case 'text':
        return { type: 'text', content: b.content };
      case 'rich-text':
        return { type: 'rich-text', body: htmlToBlocks(b.content) };
      case 'button':
        return { type: 'button', label: b.label, action: this.action(b.action) };
      default:
        throw new Error(`Unsupported block type: ${b.type}`);
    }
  }

  private async section(s: Json) {
    switch (s.type) {
      case 'image':
        return { __component: 'section.image', image: await this.media(s.src), alt: this.alt(s) };
      case 'text':
        return { __component: 'section.text', content: s.content };
      case 'rich-text':
        return { __component: 'section.rich-text', body: htmlToBlocks(s.content) };
      case 'button':
        return { __component: 'section.button', label: s.label, action: this.action(s.action) };
      case 'contact':
        return { __component: 'section.contact', label: s.label };
      case 'multiContent':
        return { __component: 'section.multi-content', blocks: await mapSeq(s.contentArray, (b: Json) => this.block(b)) };
      case 'multiPage':
        return {
          __component: 'section.multi-page',
          pages: await mapSeq(s.pageArray, async (page: Json[]) => ({ blocks: await mapSeq(page, (b) => this.block(b)) })),
        };
      default:
        throw new Error(`Unsupported section type: ${s.type}`);
    }
  }

  private async topic(country: string, locale: string, t: Json) {
    return this.upsertLocalized('api::topic.topic', `${country}:${t.id}`, locale, {
      uid: t.id,
      label: t.label,
      icon: await this.media(t.icon),
      sections: await mapSeq(t.content?.sections ?? [], (s: Json) => this.section(s)),
    });
  }

  async content(countryId: string, country: string, locale: string, kind: (typeof KINDS)[number]) {
    // Legacy data defects (scheme-less links, blank button labels) are corrected in one place, shared with parity.ts.
    const { fixed, warnings } = applyLegacyFixes(readJson(`content/resources/${country}/${locale}/${kind}.json`));
    warnings.forEach((w) => console.warn(w));
    const json = fixed as Json;
    const items: Json[] = [];

    for (const page of json.pages) {
      if (page.type === 'category') {
        const topicIds = await mapSeq(page.topics ?? [], (t: Json) => this.topic(country, locale, t));
        const categoryId = await this.upsertLocalized('api::category.category', `${country}:${page.id}`, locale, {
          uid: page.id,
          label: page.label,
          icon: await this.media(page.icon),
          topics: topicIds,
        });
        items.push({ __component: 'item.category-ref', category: categoryId });
      } else if (page.type === 'topic') {
        items.push({ __component: 'item.topic-ref', topic: await this.topic(country, locale, page) });
      } else {
        throw new Error(`Unsupported page type "${page.type}" in ${country}/${locale}/${kind}`);
      }
    }

    await this.upsertLocalized('api::content-page.content-page', `${country}:${kind}`, locale, {
      country: countryId,
      kind,
      title: json.title,
      items,
    });
  }

  /** One Tool asset document per (country, key); each of the country's locales gets its own file. */
  async toolsAssets(countryId: string, country: string, locale: string) {
    const mapping = readJson(`config/CMSToolsAssetsMapping_${country}_${locale}.json`);
    for (const [key, value] of Object.entries(mapping)) {
      const file = await this.media((value as Json).uri);
      await this.upsertLocalized('api::tool-asset.tool-asset', `${country}:${key}`, locale, { key, country: countryId, file });
    }
  }

  async featureFlags(countryId: string, country: string) {
    const foggles = readJson(`config/foggles_${country}.json`);
    const tools = Object.entries(foggles.features.tools as Json).map(([key, tool]) => ({
      key,
      enabled: (tool as Json).enabled,
      subcategories: Object.entries(((tool as Json).subcategories ?? {}) as Json).map(([subKey, sub]) => ({
        key: subKey,
        enabled: (sub as Json).enabled,
      })),
    }));
    await this.docs('api::feature-flag.feature-flag').create({ data: { country: countryId, tools } });
  }

  async country(code: string): Promise<string> {
    const doc = await this.docs('api::country.country').create({ data: { code, name: COUNTRY_NAMES[code] } });
    return doc.documentId;
  }
}

async function main() {
  const strapi = await createStrapi(await compileStrapi()).load();
  try {
    const mediaMap: MediaMap = fs.existsSync(MEDIA_MAP_FILE) ? JSON.parse(fs.readFileSync(MEDIA_MAP_FILE, 'utf8')) : {};
    const importer = new Importer(strapi, mediaMap);

    if (RESET) {
      console.log('Deleting previously imported content...');
      await importer.reset();
    } else if (await importer.hasContent()) {
      throw new Error('The CMS already has content. Re-run with --reset to delete imported content and import again.');
    }

    await importer.ensureLocales();

    for (const [country, locales] of Object.entries(TARGETS)) {
      const countryId = await importer.country(country);
      await importer.featureFlags(countryId, country);
      for (const locale of locales) {
        console.log(`Importing ${country}/${locale}...`);
        await importer.toolsAssets(countryId, country, locale);
        for (const kind of KINDS) await importer.content(countryId, country, locale, kind);
      }
    }

    console.log(`Done. Media files known: ${Object.keys(mediaMap).length}`);
  } finally {
    await waitForIdleDb(strapi);
    await strapi.destroy();
  }
}

/**
 * Strapi runs some work after publish asynchronously (fire-and-forget queries). Destroying the knex pool while
 * those still wait for a connection crashes the process with "Error: aborted", so wait for the pool to go idle.
 */
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
