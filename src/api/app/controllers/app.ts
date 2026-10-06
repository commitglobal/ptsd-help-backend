import type { Core } from '@strapi/strapi';

const KINDS = ['learn', 'support'] as const;
type Kind = (typeof KINDS)[number];

const respond = (ctx: any, body: unknown) => {
  if (!body) return ctx.notFound();
  // The app caches content itself (lastUpdatedAt); an HTTP max-age made the phone's HTTP cache
  // serve stale JSON for minutes after an edit.
  ctx.set('Cache-Control', 'no-cache');
  ctx.body = body;
};

export default ({ strapi }: { strapi: Core.Strapi }) => {
  const service = () => strapi.service('api::app.app') as any;

  return {
    async content(ctx: any) {
      const { country, locale, kind } = ctx.params;
      if (!KINDS.includes(kind)) return ctx.notFound();
      respond(ctx, await service().getContent(country.toUpperCase(), locale.toLowerCase(), kind as Kind));
    },

    async toolsAssets(ctx: any) {
      const { country, locale } = ctx.params;
      respond(ctx, await service().getToolsAssets(country.toUpperCase(), locale.toLowerCase()));
    },

    async foggles(ctx: any) {
      respond(ctx, await service().getFeatureFlags(ctx.params.country.toUpperCase()));
    },
  };
};
