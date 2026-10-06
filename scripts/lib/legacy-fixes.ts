/**
 * Corrections applied to legacy S3 content JSON before import (and to the expected side of the parity check),
 * so both scripts share one definition of "the importer's intended output".
 *
 * 1. external/webview action url without a scheme ("zinvori-mair-ngo.am/") -> "https://" prefixed
 *    (the topic validation rejects it and the app cannot open it).
 * 2. button with an empty/blank label (rendered as a blank button) -> label derived from its action target
 *    (url without scheme, phone number or tool id), since the topic validation requires a label.
 */
type Json = Record<string, any>;

const HAS_SCHEME = /^(https?:\/\/|mailto:)/;

const buttonLabelFallback = (action: Json | undefined): string => {
  const target: string = action?.url ?? action?.number ?? action?.toolId ?? '';
  return target.replace(/^https?:\/\//, '').replace(/\/$/, '') || 'Open';
};

export const applyLegacyFixes = (json: unknown): { fixed: unknown; warnings: string[] } => {
  const warnings: string[] = [];

  const walk = (value: unknown): unknown => {
    if (Array.isArray(value)) return value.map(walk);
    if (!value || typeof value !== 'object') return value;

    const source = value as Json;
    const out: Json = {};
    for (const [key, v] of Object.entries(source)) out[key] = walk(v);

    if (out.type === 'button' && (typeof out.label !== 'string' || out.label.trim() === '')) {
      out.label = buttonLabelFallback(source.action);
      warnings.push(`WARN: button without label (action ${JSON.stringify(source.action)}) imported with label "${out.label}"`);
    }

    const action = out.action as Json | undefined;
    if (
      action &&
      (action.type === 'external' || action.type === 'webview') &&
      typeof action.url === 'string' &&
      action.url !== '' &&
      !HAS_SCHEME.test(action.url)
    ) {
      warnings.push(`WARN: ${action.type} url without scheme "${action.url}" imported as "https://${action.url}"`);
      out.action = { ...action, url: `https://${action.url}` };
    }

    return out;
  };

  return { fixed: walk(json), warnings };
};
