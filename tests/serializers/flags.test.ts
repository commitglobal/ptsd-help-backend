import { serializeFeatureFlags } from '../../src/api/app/serializers/flags';

describe('serializeFeatureFlags', () => {
  it('builds the foggles structure, adding subcategories only when present', () => {
    expect(
      serializeFeatureFlags({
        updatedAt: '2026-04-04T00:00:00.000Z',
        tools: [
          { key: 'rid', enabled: false, subcategories: [] },
          {
            key: 'relationships',
            enabled: true,
            subcategories: [{ key: 'relationships-i-messages', enabled: false }],
          },
        ],
      })
    ).toEqual({
      version: '1.0.0',
      lastUpdated: '2026-04-04T00:00:00.000Z',
      features: {
        tools: {
          rid: { enabled: false },
          relationships: { enabled: true, subcategories: { 'relationships-i-messages': { enabled: false } } },
        },
      },
    });
  });
});
