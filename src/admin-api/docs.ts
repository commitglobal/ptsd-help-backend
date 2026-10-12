import type { Core } from '@strapi/strapi';

const DOC_UID = 'api::doc.doc';
const READ_ACTION = 'plugin::content-manager.explorer.read';
const UPDATE_ACTION = 'plugin::content-manager.explorer.update';

const can = (ctx: any, action: string) => Boolean(ctx.state.userAbility?.can(action, DOC_UID));

/** Docs in the order set by drag and drop on the admin "Docs" page; new docs (no order yet) go last, by title. */
const listDocs = (strapi: Core.Strapi) =>
  strapi.db.query(DOC_UID).findMany({
    select: ['documentId', 'title', 'updatedAt'],
    orderBy: [{ order: 'asc' }, { title: 'asc' }],
  });

/** Writes the position of every doc; ids not in the table are ignored. */
const saveOrder = (strapi: Core.Strapi, documentIds: string[]) =>
  strapi.db.transaction(async () => {
    for (const [order, documentId] of documentIds.entries()) {
      await strapi.db.query(DOC_UID).update({ where: { documentId }, data: { order } });
    }
  });

/** Admin-only routes behind the "Docs" page (src/admin/pages/Docs.tsx). */
export const registerDocsRoutes = (strapi: Core.Strapi) => {
  strapi.server.routes({
    type: 'admin',
    prefix: '/ptsd-admin/docs',
    routes: [
      {
        method: 'GET',
        path: '/',
        handler: async (ctx: any) => {
          if (!can(ctx, READ_ACTION)) return ctx.forbidden();
          ctx.body = { docs: await listDocs(strapi) };
        },
        config: { policies: ['admin::isAuthenticatedAdmin'] },
      },
      {
        method: 'PUT',
        path: '/order',
        handler: async (ctx: any) => {
          if (!can(ctx, UPDATE_ACTION)) return ctx.forbidden();
          const documentIds = ctx.request.body?.documentIds;
          if (!Array.isArray(documentIds) || !documentIds.every((id) => typeof id === 'string')) {
            return ctx.badRequest('documentIds trebuie să fie o listă de id-uri.');
          }
          await saveOrder(strapi, documentIds);
          ctx.body = { ok: true };
        },
        config: { policies: ['admin::isAuthenticatedAdmin'] },
      },
    ],
  } as any);
};
