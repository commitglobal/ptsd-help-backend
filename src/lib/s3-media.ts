/** Object names the S3 provider writes: sanitized hash + extension, no folders. */
const OBJECT_NAME = /^[A-Za-z0-9_-][A-Za-z0-9._-]*$/;

/** Media is content-addressed (hash in the name), so clients may cache it for a day. */
const DEFAULT_CACHE_CONTROL = 'public, max-age=86400';

/** Maps a request path like `/uploads/<hash><ext>` to its S3 object key, or null when the path is not a media file. */
export const mediaKeyFromPath = (path: string): string | null => {
  const match = /^\/uploads\/([^/]+)$/.exec(path);
  if (!match) return null;
  let name: string;
  try {
    name = decodeURIComponent(match[1]);
  } catch {
    return null;
  }
  return OBJECT_NAME.test(name) ? name : null;
};

type S3ObjectMeta = {
  CacheControl?: string;
  ContentType?: string;
  ContentLength?: number;
  ContentRange?: string;
  ETag?: string;
  LastModified?: Date;
};

export const s3ObjectHeaders = (meta: S3ObjectMeta): Record<string, string> => {
  const headers: Record<string, string> = {
    'Accept-Ranges': 'bytes',
    'Cache-Control': meta.CacheControl ?? DEFAULT_CACHE_CONTROL,
  };
  if (meta.ContentType) headers['Content-Type'] = meta.ContentType;
  if (meta.ContentLength !== undefined) headers['Content-Length'] = String(meta.ContentLength);
  if (meta.ContentRange) headers['Content-Range'] = meta.ContentRange;
  if (meta.ETag) headers.ETag = meta.ETag;
  if (meta.LastModified) headers['Last-Modified'] = meta.LastModified.toUTCString();
  return headers;
};
