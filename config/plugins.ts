import type { Core } from '@strapi/strapi';
import type { env } from '@strapi/utils';

// Strapi 5.11 does not export Core.Config.Shared, so the config params type is declared locally.
type ConfigParams = { env: typeof env };

const allowedMediaTypes = [
  'image/*',
  'video/*',
  'audio/*',
  'application/pdf',
  'application/msword',
  'application/vnd.openxmlformats-officedocument.*',
  'text/plain',
  'text/csv',
];

const deniedTypes = [
  'image/svg+xml',
  'application/vnd.microsoft.portable-executable',
  'application/x-msdownload',
  'application/x-msdos-program',
  'application/x-executable',
  'application/x-dosexec',
  'application/x-sh',
  'text/x-shellscript',
  'application/x-mach-binary',
];

const sizeLimit = 250 * 1024 * 1024;

const config = ({ env }: ConfigParams): Core.Config.Plugin => {
  const usersPermissions = {
    'users-permissions': {
      enabled: true,
      config: {
        jwtManagement: 'refresh',
        sessions: {
          httpOnly: true,
        },
      },
    },
  };

  if (env('UPLOAD_PROVIDER', 'local') !== 'aws-s3') {
    return {
      ...usersPermissions,
      upload: {
        enabled: true,
        config: {
          sizeLimit,
          security: {
            allowedTypes: allowedMediaTypes,
            deniedTypes,
          },
        },
      },
    };
  }

  return {
    ...usersPermissions,
    upload: {
      enabled: true,
      config: {
        sizeLimit,
        security: {
          allowedTypes: allowedMediaTypes,
          deniedTypes,
        },
        provider: 'aws-s3',
        providerOptions: {
          baseUrl: env('S3_PUBLIC_URL'),
          s3Options: {
            credentials: {
              accessKeyId: env('S3_ACCESS_KEY_ID'),
              secretAccessKey: env('S3_SECRET_ACCESS_KEY'),
            },
            region: env('S3_REGION'),
            params: {
              Bucket: env('S3_BUCKET'),
              ACL: env('S3_ACL', 'public-read'),
            },
          },
        },
        actionOptions: { upload: {}, uploadStream: {}, delete: {} },
      },
    },
  };
};

export default config;
