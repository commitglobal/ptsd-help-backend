import type { Core } from '@strapi/strapi';
import { CONTENT_PAGE_POPULATE, FEATURE_FLAGS_POPULATE } from '../populate';
import { latestPerKey } from '../../../lib/tool-assets';
import { serializeContentPage } from '../serializers/content';
import { serializeToolsAssets } from '../serializers/assets';
import { serializeFeatureFlags } from '../serializers/flags';
import type { AssetInput, ContentPageInput, FeatureFlagsInput } from '../serializers/types';

export default ({ strapi }: { strapi: Core.Strapi }) => {
  const baseUrl = () => strapi.config.get<string>('server.url') || 'http://localhost:1337';

  const localeExists = async (locale: string) => {
    const locales: { code: string }[] = await strapi.plugin('i18n').service('locales').find();
    return locales.some((l) => l.code === locale);
  };

  // Duplicates (two published entries for the same country/locale) are an editor mistake:
  // log it loudly and serve the most recently updated one instead of failing the app.
  const pickOne = <T>(docs: T[], label: string): T | null => {
    if (docs.length > 1) strapi.log.error(`[app] ${docs.length} published "${label}" entries found; serving the newest`);
    return docs[0] ?? null;
  };

  return {
    async getContent(country: string, locale: string, kind: 'learn' | 'support') {
      if (!(await localeExists(locale))) return null;
      const docs = await strapi.documents('api::content-page.content-page').findMany({
        locale,
        status: 'published',
        filters: { kind, country: { code: country } },
        sort: 'updatedAt:desc',
        populate: CONTENT_PAGE_POPULATE as any,
      });
      const page = pickOne(docs, `content-page ${country}/${locale}/${kind}`);
      return page ? serializeContentPage(page as unknown as ContentPageInput, baseUrl()) : null;
    },

    async getToolsAssets(country: string, locale: string) {
      if (!(await localeExists(locale))) return null;
      // No limit/pagination: the document service returns every matching entry (81 keys per country today).
      const docs = (await strapi.documents('api::tool-asset.tool-asset').findMany({
        locale,
        filters: { country: { code: country } },
        sort: 'updatedAt:desc',
        populate: { file: true },
      })) as unknown as (AssetInput & { updatedAt?: string | null })[];
      if (!docs.length) return null;
      const { entries, duplicateKeys } = latestPerKey(docs);
      if (duplicateKeys.length) {
        strapi.log.error(`[app] duplicate Tool asset keys for ${country}/${locale}: ${duplicateKeys.join(', ')}; serving the newest`);
      }
      return serializeToolsAssets(
        entries.map(({ key, file }) => ({ key, file })),
        baseUrl()
      );
    },

    async getFeatureFlags(country: string) {
      const docs = await strapi.documents('api::feature-flag.feature-flag').findMany({
        status: 'published',
        filters: { country: { code: country } },
        sort: 'updatedAt:desc',
        populate: FEATURE_FLAGS_POPULATE as any,
      });
      const entry = pickOne(docs, `feature-flags ${country}`);
      return entry ? serializeFeatureFlags(entry as unknown as FeatureFlagsInput) : null;
    },
  };
};
