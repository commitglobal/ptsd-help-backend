import type { StrapiApp } from '@strapi/strapi/admin';
import { Book } from '@strapi/icons';
import { CopyToolAssets } from './components/CopyToolAssets';

export default {
  config: {
    locales: [],
  },
  register(app: StrapiApp) {
    // "Docs" page between Home (position 0) and Content Manager (position 1).
    app.addMenuLink({
      to: 'docs',
      icon: Book,
      intlLabel: { id: 'docs.menu', defaultMessage: 'Docs' },
      Component: () => import('./pages/Docs'),
      permissions: [],
      position: 0.5,
    });
  },
  bootstrap(app: StrapiApp) {
    // "Copiază din altă țară/limbă" next to "Create new entry" in Content Manager → Tool asset.
    app.getPlugin('content-manager').injectComponent('listView', 'actions', {
      name: 'copy-tool-assets',
      Component: CopyToolAssets,
    });
  },
};
