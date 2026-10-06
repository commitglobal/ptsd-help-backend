import { firstDifference, normalize } from '../../scripts/lib/normalize';

const identity = (u: string) => u;

describe('normalize', () => {
  it('drops timestamp/version fields at any depth', () => {
    expect(
      normalize({ lastUpdatedAt: 'x', version: '1', a: { lastUpdated: 'y', b: 1, c: { uri: 'u', lastUpdatedAt: 'z' } } }, identity)
    ).toEqual({ a: { b: 1, c: { uri: 'u' } } });
  });

  it('resolves absolute urls through the resolver', () => {
    expect(normalize({ src: 'http://localhost:1337/uploads/a.png' }, () => 'https://s3/a.png')).toEqual({ src: 'https://s3/a.png' });
  });

  it('fixes legacy image alt typos ("atl" and "content") to "alt"', () => {
    expect(normalize({ type: 'image', src: 's', atl: 'A' }, identity)).toEqual({ type: 'image', src: 's', alt: 'A' });
    expect(normalize({ type: 'image', src: 's', content: 'B' }, identity)).toEqual({ type: 'image', src: 's', alt: 'B' });
  });
});

describe('firstDifference', () => {
  it('returns null for deep-equal values', () => {
    expect(firstDifference({ a: [1, { b: 2 }] }, { a: [1, { b: 2 }] })).toBeNull();
  });

  it('reports the path of the first difference', () => {
    expect(firstDifference({ a: [1, { b: 2 }] }, { a: [1, { b: 3 }] })).toBe('$.a.1.b: expected 2, got 3');
  });

  it('reports missing array items', () => {
    expect(firstDifference([1, 2], [1])).toBe('$.1: expected 2, got undefined');
  });
});
