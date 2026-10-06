import type { FeatureFlagsInput } from './types';

export const serializeFeatureFlags = (flags: FeatureFlagsInput) => ({
  version: '1.0.0',
  lastUpdated: flags.updatedAt ?? new Date(0).toISOString(),
  features: {
    tools: Object.fromEntries(
      (flags.tools ?? []).map((tool) => [
        tool.key,
        tool.subcategories?.length
          ? {
              enabled: tool.enabled,
              subcategories: Object.fromEntries(tool.subcategories.map((s) => [s.key, { enabled: s.enabled }])),
            }
          : { enabled: tool.enabled },
      ])
    ),
  },
});
