import type { Core } from '@strapi/strapi';
import type { env } from '@strapi/utils';

// Strapi 5.11 does not export Core.Config.Shared, so the config params type is declared locally.
type ConfigParams = { env: typeof env };

const config = ({ env }: ConfigParams): Core.Config.Middlewares => {
  const s3Host = env('S3_PUBLIC_HOST', '');
  const extra = s3Host ? [s3Host] : [];

  return [
    'strapi::logger',
    'strapi::errors',
    {
      name: 'strapi::security',
      config: {
        contentSecurityPolicy: {
          useDefaults: true,
          directives: {
            'script-src': ["'self'"],
            'connect-src': ["'self'", 'https:'],
            'img-src': ["'self'", 'data:', 'blob:', 'market-assets.strapi.io', ...extra],
            'media-src': ["'self'", 'data:', 'blob:', ...extra],
            upgradeInsecureRequests: null,
          },
        },
      },
    },
    'strapi::cors',
    'strapi::poweredBy',
    'strapi::query',
    {
      name: 'strapi::body',
      config: {
        formLimit: '256mb',
        formidable: { maxFileSize: 250 * 1024 * 1024 },
      },
    },
    'strapi::session',
    'strapi::favicon',
    'strapi::public',
  ];
};

export default config;
