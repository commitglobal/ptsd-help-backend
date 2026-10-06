import { toAbsoluteUrl } from './content';
import type { AssetInput } from './types';

export const serializeToolsAssets = (assets: AssetInput[], baseUrl: string) =>
  Object.fromEntries(
    assets
      .filter((asset) => asset.file?.url)
      .map((asset) => [
        asset.key,
        { uri: toAbsoluteUrl(asset.file, baseUrl), lastUpdatedAt: asset.file?.updatedAt ?? new Date(0).toISOString() },
      ])
  );
