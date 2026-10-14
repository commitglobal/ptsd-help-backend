import { GetObjectCommand, HeadObjectCommand, S3Client } from '@aws-sdk/client-s3';
import type { Core } from '@strapi/strapi';
import { mediaKeyFromPath, s3ObjectHeaders } from '../lib/s3-media';

/**
 * Serves `/uploads/<file>` from the S3 bucket when the upload provider is aws-s3, so the app and the admin
 * only ever talk to Strapi. The provider stores relative `/uploads/...` urls (see config/plugins.ts),
 * the same shape as the local provider. With the local provider this middleware does nothing.
 */
export default (_config: unknown, { strapi }: { strapi: Core.Strapi }) => {
  if (strapi.config.get('plugin::upload.provider') !== 'aws-s3') {
    return async (_ctx: any, next: () => Promise<void>) => next();
  }

  const { params, ...clientOptions } = strapi.config.get<any>('plugin::upload.providerOptions').s3Options;
  const client = new S3Client(clientOptions);

  return async (ctx: any, next: () => Promise<void>) => {
    if (ctx.method !== 'GET' && ctx.method !== 'HEAD') return next();
    const key = mediaKeyFromPath(ctx.path);
    if (!key) return next();

    const input = {
      Bucket: params.Bucket,
      Key: key,
      Range: ctx.get('range') || undefined,
      IfNoneMatch: ctx.get('if-none-match') || undefined,
    };

    try {
      if (ctx.method === 'HEAD') {
        const meta = await client.send(new HeadObjectCommand(input));
        ctx.set(s3ObjectHeaders(meta));
        ctx.status = 200;
        return;
      }
      const object = await client.send(new GetObjectCommand(input));
      // Body first: koa would otherwise reset Content-Type to application/octet-stream for a stream.
      ctx.body = object.Body;
      ctx.set(s3ObjectHeaders(object));
      ctx.status = object.ContentRange ? 206 : 200;
    } catch (error: any) {
      const status = error?.$metadata?.httpStatusCode;
      if (status === 304 || status === 404 || status === 416) {
        ctx.status = status;
        return;
      }
      if (error?.name === 'NoSuchKey' || error?.name === 'NotFound') {
        ctx.status = 404;
        return;
      }
      throw error;
    }
  };
};
