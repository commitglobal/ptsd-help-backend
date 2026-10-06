import { planToolAssetCopy } from '../../src/lib/tool-asset-copy';

describe('planToolAssetCopy', () => {
  it('creates every source key the destination country does not have', () => {
    const source = [
      { key: 'A', fileId: 1 },
      { key: 'B', fileId: 2 },
    ];
    expect(planToolAssetCopy(source, [], 'fr')).toEqual({
      create: [
        { key: 'A', fileId: 1 },
        { key: 'B', fileId: 2 },
      ],
      addLocale: [],
      skipped: [],
    });
  });

  it('adds the locale to an existing document that lacks it, keeping its documentId', () => {
    const source = [{ key: 'A', fileId: 1 }];
    const destination = [{ documentId: 'doc-a', key: 'A', locales: ['ro', 'en'] }];
    expect(planToolAssetCopy(source, destination, 'uk')).toEqual({
      create: [],
      addLocale: [{ documentId: 'doc-a', key: 'A', fileId: 1 }],
      skipped: [],
    });
  });

  it('skips keys that already have the destination locale, never overwriting them', () => {
    const source = [
      { key: 'A', fileId: 1 },
      { key: 'B', fileId: 2 },
    ];
    const destination = [{ documentId: 'doc-a', key: 'A', locales: ['fr'] }];
    expect(planToolAssetCopy(source, destination, 'fr')).toEqual({
      create: [{ key: 'B', fileId: 2 }],
      addLocale: [],
      skipped: ['A'],
    });
  });

  it('ignores source entries without a file', () => {
    const source = [
      { key: 'A', fileId: null },
      { key: 'B', fileId: 2 },
    ];
    expect(planToolAssetCopy(source, [], 'fr').create).toEqual([{ key: 'B', fileId: 2 }]);
  });

  it('copies a key only once when the source has it twice', () => {
    const source = [
      { key: 'A', fileId: 1 },
      { key: 'A', fileId: 9 },
    ];
    expect(planToolAssetCopy(source, [], 'fr').create).toEqual([{ key: 'A', fileId: 1 }]);
  });
});
