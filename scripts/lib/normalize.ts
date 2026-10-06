const DROPPED_KEYS = new Set(['lastUpdatedAt', 'lastUpdated', 'version']);

export const normalize = (value: unknown, resolveUrl: (url: string) => string): unknown => {
  if (Array.isArray(value)) return value.map((v) => normalize(v, resolveUrl));

  if (value && typeof value === 'object') {
    const source = value as Record<string, unknown>;
    const out: Record<string, unknown> = {};
    for (const [key, v] of Object.entries(source)) {
      if (!DROPPED_KEYS.has(key)) out[key] = normalize(v, resolveUrl);
    }
    // Legacy S3 JSON has a few images with "atl" or "content" instead of "alt".
    if (out.type === 'image' && out.alt === undefined) {
      out.alt = source.atl ?? source.content ?? '';
      delete out.atl;
      delete out.content;
    }
    return out;
  }

  if (typeof value === 'string' && /^https?:\/\//.test(value)) return resolveUrl(value);
  return value;
};

const show = (v: unknown) => (v === undefined ? 'undefined' : JSON.stringify(v).slice(0, 160));

export const firstDifference = (expected: unknown, actual: unknown, path = '$'): string | null => {
  if (Object.is(expected, actual)) return null;

  const bothObjects =
    expected !== null && actual !== null && typeof expected === 'object' && typeof actual === 'object';
  if (!bothObjects || Array.isArray(expected) !== Array.isArray(actual)) {
    return `${path}: expected ${show(expected)}, got ${show(actual)}`;
  }

  const keys = new Set([...Object.keys(expected as object), ...Object.keys(actual as object)]);
  for (const key of keys) {
    const diff = firstDifference((expected as any)[key], (actual as any)[key], `${path}.${key}`);
    if (diff) return diff;
  }
  return null;
};
