import { mediaKeyFromPath, s3ObjectHeaders } from '../../src/lib/s3-media';

describe('mediaKeyFromPath', () => {
  it('returns the object key for an /uploads file', () => {
    expect(mediaKeyFromPath('/uploads/list_category_icon_3fd4381356.jpg')).toBe('list_category_icon_3fd4381356.jpg');
  });

  it('decodes url-encoded names', () => {
    expect(mediaKeyFromPath('/uploads/thumbnail_a%2Db.png')).toBe('thumbnail_a-b.png');
  });

  it.each([
    '/api/app/content/RO/ro/learn',
    '/uploads/',
    '/uploads/a/b.jpg',
    '/uploads/a%2Fb.jpg',
    '/uploads/..',
    '/uploads/.env',
    '/uploads/a b.jpg',
    '/uploads/%E0%A4%A',
  ])('ignores %s', (path) => {
    expect(mediaKeyFromPath(path)).toBeNull();
  });
});

describe('s3ObjectHeaders', () => {
  it('maps the S3 object metadata to response headers', () => {
    expect(
      s3ObjectHeaders({
        ContentType: 'audio/mpeg',
        ContentLength: 1024,
        ContentRange: 'bytes 0-1023/4096',
        ETag: '"abc"',
        LastModified: new Date('2026-10-14T10:00:00.000Z'),
      })
    ).toEqual({
      'Accept-Ranges': 'bytes',
      'Cache-Control': 'public, max-age=86400',
      'Content-Type': 'audio/mpeg',
      'Content-Length': '1024',
      'Content-Range': 'bytes 0-1023/4096',
      ETag: '"abc"',
      'Last-Modified': 'Wed, 14 Oct 2026 10:00:00 GMT',
    });
  });

  it('keeps the Cache-Control stored on the object and skips missing fields', () => {
    expect(s3ObjectHeaders({ CacheControl: 'no-cache' })).toEqual({
      'Accept-Ranges': 'bytes',
      'Cache-Control': 'no-cache',
    });
  });
});
