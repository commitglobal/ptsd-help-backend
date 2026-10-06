import type { Core } from '@strapi/strapi';
import { errors } from '@strapi/utils';
import { validateSections } from './validation/sections';
import { withGeneratedUid } from './lib/uid';
import {
  duplicateToolAssetMessage,
  extractRelationRef,
  findConflictingDocumentId,
  immutableToolAssetProblem,
  resolveToolAssetTarget,
} from './validation/tool-asset';
import { countryLanguageProblem } from './constants/country-languages';
import { registerToolAssetCopyRoutes } from './admin-api/tool-asset-copy';

const WRITE_ACTIONS = new Set(['create', 'update']);
const UID_CONTENT_TYPES = new Set(['api::topic.topic', 'api::category.category']);
const TOOL_ASSET_UID = 'api::tool-asset.tool-asset';
/** Localized content types that belong to a country: their language must be one the app offers for that country. */
const COUNTRY_LOCALIZED_TYPES = new Set(['api::content-page.content-page', TOOL_ASSET_UID]);
/** Field each relation picker shows for its target, instead of the uid / document id. */
const RELATION_MAIN_FIELD: Record<string, string> = {
  'api::category.category': 'label',
  'api::topic.topic': 'label',
  'api::country.country': 'name',
};

const findExistingWithCountry = (strapi: Core.Strapi, uid: string, action: string, params: Record<string, any>) =>
  action === 'update' && params.documentId
    ? strapi.db.query(uid as any).findOne({ where: { documentId: params.documentId }, populate: { country: true } })
    : Promise.resolve(null);

const assertCountryLanguage = async (strapi: Core.Strapi, uid: string, action: string, params: Record<string, any>) => {
  const existing = await findExistingWithCountry(strapi, uid, action, params);
  const fromData = extractRelationRef(params.data?.country);
  const countryRef = fromData !== undefined ? fromData : extractRelationRef(existing?.country ?? null);
  if (!countryRef) return;

  const countryRow = await strapi.db.query('api::country.country').findOne({ where: countryRef });
  if (!countryRow) return;

  const locale: string = params.locale ?? (await strapi.plugin('i18n').service('locales').getDefaultLocale());
  const problem = countryLanguageProblem(countryRow.code, locale);
  if (problem) throw new errors.ValidationError(problem);
};

/**
 * One Tool asset document per (country, key); other locales of the same document are allowed.
 * Key and country cannot change after creation.
 */
const assertUniqueToolAsset = async (strapi: Core.Strapi, action: string, params: Record<string, any>) => {
  const documentId: string | undefined = action === 'update' ? params.documentId : undefined;
  const existing = await findExistingWithCountry(strapi, TOOL_ASSET_UID, action, params);

  const immutableProblem = immutableToolAssetProblem(params.data, existing);
  if (immutableProblem) throw new errors.ValidationError(immutableProblem);

  const { key, country } = resolveToolAssetTarget(params.data, existing);
  if (!key || !country) return;

  const countryRow = await strapi.db.query('api::country.country').findOne({ where: country });
  if (!countryRow) return;

  const sameKeyAndCountry = await strapi.db.query(TOOL_ASSET_UID).findMany({
    select: ['documentId'],
    where: { key, country: { id: countryRow.id } },
  });
  if (findConflictingDocumentId(documentId, sameKeyAndCountry)) {
    throw new errors.ValidationError(duplicateToolAssetMessage(key, countryRow.code));
  }
};

export default {
  register({ strapi }: { strapi: Core.Strapi }) {
    registerToolAssetCopyRoutes(strapi);

    strapi.documents.use(async (context, next) => {
      if (UID_CONTENT_TYPES.has(context.uid) && WRITE_ACTIONS.has(context.action)) {
        const params = context.params as { data?: Record<string, unknown> };
        params.data = withGeneratedUid(context.action, params.data);
      }

      if (context.uid === 'api::topic.topic' && WRITE_ACTIONS.has(context.action)) {
        const data = (context.params as { data?: { sections?: unknown[] } }).data;
        if (data && 'sections' in data) {
          const problems = validateSections(data.sections);
          if (problems.length) throw new errors.ValidationError(problems.join('\n'));
        }
      }

      if (COUNTRY_LOCALIZED_TYPES.has(context.uid) && WRITE_ACTIONS.has(context.action)) {
        await assertCountryLanguage(strapi, context.uid, context.action, context.params as Record<string, any>);
      }

      if (context.uid === TOOL_ASSET_UID && WRITE_ACTIONS.has(context.action)) {
        await assertUniqueToolAsset(strapi, context.action, context.params as Record<string, any>);
      }
      return next();
    });
  },

  async bootstrap({ strapi }: { strapi: Core.Strapi }) {
    // `uid` is generated on Create (see register), so editors see it as a read-only field.
    // Relation pickers (e.g. Content page → items) show the label instead of the document id.
    const contentTypes = strapi.plugin('content-manager').service('content-types');
    for (const contentTypeUid of UID_CONTENT_TYPES) {
      const contentType = contentTypes.findContentType(contentTypeUid);
      const { settings, metadatas, layouts } = await contentTypes.findConfiguration(contentType);
      const uidEdit = metadatas?.uid?.edit ?? {};
      if (uidEdit.editable === false && settings?.mainField === 'label') continue;
      await contentTypes.updateConfiguration(contentType, {
        settings: { ...settings, mainField: 'label' },
        layouts,
        metadatas: {
          ...metadatas,
          uid: {
            ...metadatas?.uid,
            edit: { ...uidEdit, editable: false, description: 'Se generează automat la Create' },
          },
        },
      });
    }

    // Country picker shows the name instead of the document id.
    const country = contentTypes.findContentType('api::country.country');
    const countryConfig = await contentTypes.findConfiguration(country);
    if (countryConfig.settings?.mainField !== 'name') {
      await contentTypes.updateConfiguration(country, {
        layouts: countryConfig.layouts,
        metadatas: countryConfig.metadatas,
        settings: { ...countryConfig.settings, mainField: 'name' },
      });
    }

    // Tool assets: one entry per file; the list shows key, country, file and last update, sorted by key.
    const toolAsset = contentTypes.findContentType(TOOL_ASSET_UID);
    const toolAssetConfig = await contentTypes.findConfiguration(toolAsset);
    const toolAssetSettings = {
      ...toolAssetConfig.settings,
      mainField: 'key',
      defaultSortBy: 'key',
      defaultSortOrder: 'ASC',
      searchable: true,
      filterable: true,
    };
    const toolAssetLayouts = { ...toolAssetConfig.layouts, list: ['key', 'country', 'file', 'updatedAt'] };
    // Key and country are chosen on Create and then locked server-side (see assertUniqueToolAsset);
    // the fields stay editable because Strapi uses the same form for Create and Edit.
    const fixedAfterCreate = (field: 'key' | 'country') => {
      const metadata = toolAssetConfig.metadatas?.[field] ?? {};
      return {
        ...metadata,
        edit: { ...metadata.edit, editable: true, description: 'Se alege la Create și nu se mai poate schimba după' },
      };
    };
    const toolAssetMetadatas = {
      ...toolAssetConfig.metadatas,
      key: fixedAfterCreate('key'),
      country: fixedAfterCreate('country'),
    };
    if (
      JSON.stringify(toolAssetSettings) !== JSON.stringify(toolAssetConfig.settings) ||
      JSON.stringify(toolAssetLayouts) !== JSON.stringify(toolAssetConfig.layouts) ||
      JSON.stringify(toolAssetMetadatas) !== JSON.stringify(toolAssetConfig.metadatas)
    ) {
      await contentTypes.updateConfiguration(toolAsset, {
        settings: toolAssetSettings,
        layouts: toolAssetLayouts,
        metadatas: toolAssetMetadatas,
      });
    }

    // Relation pickers read the field's own `mainField` (per content type / component), not the
    // target's settings, so point every relation at a readable field instead of `uid` / document id.
    const components = strapi.plugin('content-manager').service('components');
    const editableModels = [
      ...Object.values(strapi.contentTypes)
        .filter((model) => model.uid.startsWith('api::'))
        .map((model) => ({ model: contentTypes.findContentType(model.uid), service: contentTypes })),
      ...Object.keys(strapi.components).map((uid) => ({ model: components.findComponent(uid), service: components })),
    ];
    for (const { model, service } of editableModels) {
      const config = await service.findConfiguration(model);
      let changed = false;
      const metadatas = { ...config.metadatas };
      for (const [field, attribute] of Object.entries(model.attributes as Record<string, any>)) {
        const mainField = attribute.type === 'relation' ? RELATION_MAIN_FIELD[attribute.target] : undefined;
        if (!mainField || metadatas[field]?.edit?.mainField === mainField) continue;
        metadatas[field] = { ...metadatas[field], edit: { ...metadatas[field]?.edit, mainField } };
        changed = true;
      }
      if (changed) {
        await service.updateConfiguration(model, {
          settings: config.settings,
          layouts: config.layouts,
          metadatas,
        });
      }
    }

    const publicRole = await strapi.db.query('plugin::users-permissions.role').findOne({
      where: { type: 'public' },
      populate: { permissions: true },
    });
    // Strapi 5.11's deleteMany builds invalid Postgres SQL for a relation filter (`where: { role }`),
    // so delete the public role's permissions by id instead.
    const permissionIds = ((publicRole?.permissions ?? []) as { id: number }[]).map((p) => p.id);
    if (permissionIds.length) {
      await strapi.db.query('plugin::users-permissions.permission').deleteMany({
        where: { id: { $in: permissionIds } },
      });
    }

    const advanced = await strapi
      .store({ type: 'plugin', name: 'users-permissions', key: 'advanced' })
      .get();
    await strapi.store({ type: 'plugin', name: 'users-permissions', key: 'advanced' }).set({
      value: { ...(advanced as Record<string, unknown>), allow_register: false },
    });
  },
};
