import { serializeToolsAssets } from '../../src/api/app/serializers/assets';

describe('serializeToolsAssets', () => {
  it('maps each key to an absolute uri and the file updatedAt', () => {
    expect(
      serializeToolsAssets(
        [
          { key: 'RID.CATEGORY_ICON', file: { url: '/uploads/rid.jpg', updatedAt: '2026-02-02T00:00:00.000Z' } },
          { key: 'AMBIENT_SOUNDS.BEACH.soundURI', file: { url: 'https://cdn.example.com/beach.mp3', updatedAt: '2026-03-03T00:00:00.000Z' } },
        ],
        'http://localhost:1337'
      )
    ).toEqual({
      'RID.CATEGORY_ICON': { uri: 'http://localhost:1337/uploads/rid.jpg', lastUpdatedAt: '2026-02-02T00:00:00.000Z' },
      'AMBIENT_SOUNDS.BEACH.soundURI': { uri: 'https://cdn.example.com/beach.mp3', lastUpdatedAt: '2026-03-03T00:00:00.000Z' },
    });
  });

  it('drops keys without a file', () => {
    expect(serializeToolsAssets([{ key: 'RID.CATEGORY_ICON', file: null }], 'http://x')).toEqual({});
  });
});
