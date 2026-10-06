import { latestPerKey } from '../../src/lib/tool-assets';

describe('latestPerKey', () => {
  it('keeps every entry when keys are unique', () => {
    const entries = [
      { key: 'A', updatedAt: '2026-01-01T00:00:00.000Z', file: { url: '/a' } },
      { key: 'B', updatedAt: '2026-01-02T00:00:00.000Z', file: { url: '/b' } },
    ];
    expect(latestPerKey(entries)).toEqual({ entries, duplicateKeys: [] });
  });

  it('keeps the most recently updated entry for a duplicated key, in any order', () => {
    const older = { key: 'A', updatedAt: '2026-01-01T00:00:00.000Z', file: { url: '/old' } };
    const newer = { key: 'A', updatedAt: '2026-05-01T00:00:00.000Z', file: { url: '/new' } };
    const other = { key: 'B', updatedAt: '2026-01-01T00:00:00.000Z', file: { url: '/b' } };
    expect(latestPerKey([older, other, newer])).toEqual({ entries: [newer, other], duplicateKeys: ['A'] });
    expect(latestPerKey([newer, other, older])).toEqual({ entries: [newer, other], duplicateKeys: ['A'] });
  });

  it('treats a missing updatedAt as oldest', () => {
    const noDate = { key: 'A', file: { url: '/x' } };
    const dated = { key: 'A', updatedAt: '2026-01-01T00:00:00.000Z', file: { url: '/y' } };
    expect(latestPerKey([dated, noDate]).entries).toEqual([dated]);
  });
});
