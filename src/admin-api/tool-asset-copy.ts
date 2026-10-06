import type { Core } from '@strapi/strapi';
import { COUNTRY_LANGUAGES, countryLanguageProblem } from '../constants/country-languages';
import { planToolAssetCopy, type DestinationDocument } from '../lib/tool-asset-copy';

const TOOL_ASSET_UID = 'api::tool-asset.tool-asset';
const CREATE_ACTION = 'plugin::content-manager.explorer.create';

type CopyRequest = { fromCountry?: string; fromLocale?: string; toCountry?: string; toLocale?: string };

/** Country/language pairs offered in the admin dialog: every country row with its languages from COUNTRY_LANGUAGES. */
const getOptions = async (strapi: Core.Strapi) => {
  const countries: { code: string; name: string }[] = await strapi.db
    .query('api::country.country')
    .findMany({ select: ['code', 'name'], orderBy: { name: 'asc' } });
  return countries.map(({ code, name }) => ({ code, name, languages: [...(COUNTRY_LANGUAGES[code] ?? [])] }));
};

const copy = async (strapi: Core.Strapi, request: CopyRequest) => {
  const fromCountry = request.fromCountry?.toUpperCase();
  const toCountry = request.toCountry?.toUpperCase();
  const fromLocale = request.fromLocale?.toLowerCase();
  const toLocale = request.toLocale?.toLowerCase();
  if (!fromCountry || !toCountry || !fromLocale || !toLocale) return { error: 'Alege sursa și destinația.' };
  if (fromCountry === toCountry && fromLocale === toLocale) return { error: 'Sursa și destinația sunt identice.' };

  const languageProblem = countryLanguageProblem(toCountry, toLocale);
  if (languageProblem) return { error: languageProblem };

  const locales: { code: string }[] = await strapi.plugin('i18n').service('locales').find();
  if (!locales.some((l) => l.code === toLocale)) {
    return { error: `Limba "${toLocale}" nu există în Settings → Internationalization.` };
  }

  const destinationCountry = await strapi.db.query('api::country.country').findOne({ where: { code: toCountry } });
  if (!destinationCountry) return { error: `Țara ${toCountry} nu există în tabela Country.` };

  // Newest first, so a duplicated source key copies its most recently updated file.
  const sourceDocs = (await strapi.documents(TOOL_ASSET_UID).findMany({
    locale: fromLocale,
    filters: { country: { code: fromCountry } },
    sort: 'updatedAt:desc',
    populate: { file: true },
  })) as unknown as { key: string; file?: { id: number } | null }[];
  if (!sourceDocs.length) return { error: `Nu există Tool asset-uri pentru ${fromCountry} / ${fromLocale}.` };

  const destinationRows: { documentId: string; key: string; locale: string }[] = await strapi.db
    .query(TOOL_ASSET_UID)
    .findMany({ select: ['documentId', 'key', 'locale'], where: { country: { code: toCountry } } });
  const destination = new Map<string, DestinationDocument>();
  for (const row of destinationRows) {
    const doc = destination.get(row.documentId) ?? { documentId: row.documentId, key: row.key, locales: [] };
    doc.locales.push(row.locale);
    destination.set(row.documentId, doc);
  }

  const plan = planToolAssetCopy(
    sourceDocs.map((doc) => ({ key: doc.key, fileId: doc.file?.id ?? null })),
    [...destination.values()],
    toLocale
  );

  // Same media file, no upload: the new entries point at the files the source already uses.
  const country = destinationCountry.documentId;
  const failed: { key: string; message: string }[] = [];
  let created = 0;
  let addedLocale = 0;
  for (const { key, fileId } of plan.create) {
    try {
      await strapi.documents(TOOL_ASSET_UID).create({ locale: toLocale, data: { key, country, file: fileId } as any });
      created++;
    } catch (error) {
      failed.push({ key, message: (error as Error).message });
    }
  }
  for (const { documentId, key, fileId } of plan.addLocale) {
    try {
      await strapi
        .documents(TOOL_ASSET_UID)
        .update({ documentId, locale: toLocale, data: { key, country, file: fileId } as any });
      addedLocale++;
    } catch (error) {
      failed.push({ key, message: (error as Error).message });
    }
  }

  return { created, addedLocale, skipped: plan.skipped.length, failed };
};

const canCreateToolAssets = (ctx: any) => Boolean(ctx.state.userAbility?.can(CREATE_ACTION, TOOL_ASSET_UID));

/**
 * Admin-only routes behind the "Copiază din altă țară/limbă" button in Content Manager → Tool asset
 * (src/admin/app.tsx). They use the admin login, not the public content API.
 */
export const registerToolAssetCopyRoutes = (strapi: Core.Strapi) => {
  strapi.server.routes({
    type: 'admin',
    prefix: '/ptsd-admin/tool-assets',
    routes: [
      {
        method: 'GET',
        path: '/copy-options',
        handler: async (ctx: any) => {
          if (!canCreateToolAssets(ctx)) return ctx.forbidden();
          ctx.body = { countries: await getOptions(strapi) };
        },
        config: { policies: ['admin::isAuthenticatedAdmin'] },
      },
      {
        method: 'POST',
        path: '/copy',
        handler: async (ctx: any) => {
          if (!canCreateToolAssets(ctx)) return ctx.forbidden();
          const result = await copy(strapi, ctx.request.body ?? {});
          if ('error' in result) return ctx.badRequest(result.error);
          ctx.body = result;
        },
        config: { policies: ['admin::isAuthenticatedAdmin'] },
      },
    ],
  } as any);
};
