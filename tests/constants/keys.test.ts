import { ASSET_KEYS, TOOL_KEYS, SUBTOOL_KEYS, APP_TOOL_IDS } from '../../src/constants/keys';

describe('generated keys', () => {
  it('has all 81 asset keys from the S3 mapping', () => {
    expect(ASSET_KEYS).toHaveLength(81);
    expect(ASSET_KEYS).toContain('RELATIONSHIPS.CATEGORY_ICON');
    expect(ASSET_KEYS).toContain('AMBIENT_SOUNDS.BEACH.soundURI');
  });

  it('has all 21 app tools and 22 subcategories as feature-flag keys', () => {
    expect(TOOL_KEYS).toHaveLength(21);
    expect(TOOL_KEYS).toContain('relationships');
    expect(TOOL_KEYS).toContain('pause');
    expect(SUBTOOL_KEYS).toHaveLength(22);
    expect(SUBTOOL_KEYS).toContain('mindfulness-loving-kindness');
    expect(SUBTOOL_KEYS).toContain('clouds');
  });

  it('has the tool ids the app can start from content buttons', () => {
    expect(APP_TOOL_IDS).toContain('rid');
    expect(APP_TOOL_IDS).toContain('mindfulness-conscious-breathing');
    expect(APP_TOOL_IDS).toContain('deep-breathing');
  });
});

describe('generated tool-asset schema', () => {
  // eslint-disable-next-line @typescript-eslint/no-var-requires
  const schema = require('../../src/api/tool-asset/content-types/tool-asset/schema.json');

  it('uses all 81 asset keys as the non-localized, required `key` enum', () => {
    expect(schema.attributes.key.enum).toEqual([...ASSET_KEYS]);
    expect(schema.attributes.key.enum).toHaveLength(81);
    expect(schema.attributes.key.required).toBe(true);
    expect(schema.attributes.key.pluginOptions.i18n.localized).toBe(false);
  });

  it('is localized, without draft & publish, with a localized file and a non-localized country', () => {
    expect(schema.pluginOptions.i18n.localized).toBe(true);
    expect(schema.options.draftAndPublish).toBe(false);
    expect(schema.attributes.file.pluginOptions.i18n.localized).toBe(true);
    expect(schema.attributes.file.required).toBe(true);
    expect(schema.attributes.country.pluginOptions.i18n.localized).toBe(false);
    expect(schema.attributes.country.target).toBe('api::country.country');
  });
});

describe('removed Tools assets type', () => {
  it('no longer has the shared.asset component or the old content type', () => {
    const fs = require('node:fs');
    const path = require('node:path');
    const root = path.join(__dirname, '..', '..');
    expect(fs.existsSync(path.join(root, 'src/components/shared/asset.json'))).toBe(false);
    expect(fs.existsSync(path.join(root, 'src/api/tools-assets'))).toBe(false);
    expect(fs.readFileSync(path.join(root, 'scripts/generate-keys.mjs'), 'utf8')).not.toContain('shared/asset.json');
  });
});
