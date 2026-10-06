import type { StrapiApp } from '@strapi/strapi/admin';
import { CopyToolAssets } from './components/CopyToolAssets';

export default {
  config: {
    locales: [],
  },
  bootstrap(app: StrapiApp) {
    // "Copiază din altă țară/limbă" next to "Create new entry" in Content Manager → Tool asset.
    app.getPlugin('content-manager').injectComponent('listView', 'actions', {
      name: 'copy-tool-assets',
      Component: CopyToolAssets,
    });
  },
};
