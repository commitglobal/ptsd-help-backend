export default {
  routes: [
    { method: 'GET', path: '/app/config/foggles/:country', handler: 'app.foggles', config: { auth: false } },
    { method: 'GET', path: '/app/config/tools-assets/:country/:locale', handler: 'app.toolsAssets', config: { auth: false } },
    { method: 'GET', path: '/app/content/:country/:locale/:kind', handler: 'app.content', config: { auth: false } },
  ],
};
