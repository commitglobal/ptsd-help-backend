const send = jest.fn();

jest.mock('@aws-sdk/client-s3', () => ({
  S3Client: jest.fn(() => ({ send })),
  GetObjectCommand: jest.fn((input) => ({ kind: 'get', input })),
  HeadObjectCommand: jest.fn((input) => ({ kind: 'head', input })),
}));

import { Readable } from 'node:stream';
import s3Media from '../../src/middlewares/s3-media';

const strapiWith = (provider: string) =>
  ({
    config: {
      get: (key: string) =>
        key === 'plugin::upload.provider'
          ? provider
          : { s3Options: { region: 'eu-central-1', params: { Bucket: 'media' } } },
    },
  }) as any;

const ctxFor = (method: string, path: string, headers: Record<string, string> = {}) => {
  const set: Record<string, string> = {};
  return {
    method,
    path,
    get: (name: string) => headers[name] ?? '',
    set: (h: Record<string, string>) => Object.assign(set, h),
    headers: set,
    status: 404,
    body: undefined as unknown,
  };
};

beforeEach(() => send.mockReset());

describe('s3-media middleware', () => {
  it('passes everything through with the local provider', async () => {
    const next = jest.fn();
    await s3Media({}, { strapi: strapiWith('local') })(ctxFor('GET', '/uploads/a.jpg'), next);
    expect(next).toHaveBeenCalled();
    expect(send).not.toHaveBeenCalled();
  });

  it('passes non-media paths through', async () => {
    const next = jest.fn();
    await s3Media({}, { strapi: strapiWith('aws-s3') })(ctxFor('GET', '/api/app/content/RO/ro/learn'), next);
    expect(next).toHaveBeenCalled();
    expect(send).not.toHaveBeenCalled();
  });

  it('streams the object from the bucket', async () => {
    const body = Readable.from(['x']);
    send.mockResolvedValue({ Body: body, ContentType: 'image/jpeg', ContentLength: 1 });
    const ctx = ctxFor('GET', '/uploads/a.jpg');
    const next = jest.fn();
    await s3Media({}, { strapi: strapiWith('aws-s3') })(ctx, next);
    expect(next).not.toHaveBeenCalled();
    expect(send).toHaveBeenCalledWith({
      kind: 'get',
      input: { Bucket: 'media', Key: 'a.jpg', Range: undefined, IfNoneMatch: undefined },
    });
    expect(ctx.status).toBe(200);
    expect(ctx.body).toBe(body);
    expect(ctx.headers['Content-Type']).toBe('image/jpeg');
  });

  it('forwards Range and answers 206', async () => {
    send.mockResolvedValue({ Body: Readable.from(['x']), ContentRange: 'bytes 0-0/10', ContentLength: 1 });
    const ctx = ctxFor('GET', '/uploads/a.mp3', { range: 'bytes=0-0' });
    await s3Media({}, { strapi: strapiWith('aws-s3') })(ctx, jest.fn());
    expect(send.mock.calls[0][0].input.Range).toBe('bytes=0-0');
    expect(ctx.status).toBe(206);
    expect(ctx.headers['Content-Range']).toBe('bytes 0-0/10');
  });

  it('answers HEAD from the object metadata without a body', async () => {
    send.mockResolvedValue({ ContentType: 'audio/mpeg', ContentLength: 10 });
    const ctx = ctxFor('HEAD', '/uploads/a.mp3');
    await s3Media({}, { strapi: strapiWith('aws-s3') })(ctx, jest.fn());
    expect(send.mock.calls[0][0].kind).toBe('head');
    expect(ctx.status).toBe(200);
    expect(ctx.body).toBeUndefined();
    expect(ctx.headers['Content-Length']).toBe('10');
  });

  it.each([
    [{ name: 'NoSuchKey', $metadata: { httpStatusCode: 404 } }, 404],
    [{ name: 'NotModified', $metadata: { httpStatusCode: 304 } }, 304],
    [{ name: 'InvalidRange', $metadata: { httpStatusCode: 416 } }, 416],
  ])('maps S3 error %o to %i', async (error, status) => {
    send.mockRejectedValue(error);
    const ctx = ctxFor('GET', '/uploads/a.jpg');
    await s3Media({}, { strapi: strapiWith('aws-s3') })(ctx, jest.fn());
    expect(ctx.status).toBe(status);
  });

  it('rethrows other S3 errors', async () => {
    send.mockRejectedValue({ name: 'AccessDenied', $metadata: { httpStatusCode: 403 } });
    await expect(
      s3Media({}, { strapi: strapiWith('aws-s3') })(ctxFor('GET', '/uploads/a.jpg'), jest.fn())
    ).rejects.toMatchObject({ name: 'AccessDenied' });
  });
});
