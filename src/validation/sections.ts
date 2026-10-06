import { APP_TOOL_IDS } from '../constants/keys';

type AnyRecord = Record<string, any>;

const isBlank = (value: unknown) => value === undefined || value === null || value === '';

/** Strapi blocks value with at least one node. */
const hasBlocks = (value: unknown) => Array.isArray(value) && value.length > 0;

const validateAction = (action: AnyRecord | null | undefined, path: string): string[] => {
  if (!action) return [`${path}: action is required`];
  switch (action.type) {
    case 'start_tool':
      return APP_TOOL_IDS.includes(action.toolId) ? [] : [`${path}.action: unknown toolId "${action.toolId ?? ''}"`];
    case 'external':
    case 'webview':
      return /^(https?:\/\/|mailto:)/.test(action.url ?? '')
        ? []
        : [`${path}.action: url must start with http://, https:// or mailto:`];
    case 'phone':
      return isBlank(action.number) ? [`${path}.action: number is required`] : [];
    case 'share':
      return isBlank(action.message) ? [`${path}.action: message is required`] : [];
    default:
      return [`${path}.action: unknown type "${action.type}"`];
  }
};

const validateBlock = (block: AnyRecord, path: string): string[] => {
  switch (block.type) {
    case 'image':
      return isBlank(block.image) ? [`${path}: image is required`] : [];
    case 'text':
      return isBlank(block.content) ? [`${path}: content is required`] : [];
    case 'rich-text':
      return hasBlocks(block.body) ? [] : [`${path}: body is required`];
    case 'button':
      if (isBlank(block.label)) return [`${path}: label is required`];
      if (!block.action) return [`${path}: action is required`];
      return validateAction(block.action, path);
    default:
      return [`${path}: unknown block type "${block.type}"`];
  }
};

const validateSection = (section: AnyRecord, path: string): string[] => {
  switch (section.__component) {
    case 'section.image':
      return isBlank(section.image) ? [`${path}: image is required`] : [];
    case 'section.text':
      return isBlank(section.content) ? [`${path}: content is required`] : [];
    case 'section.rich-text':
      return hasBlocks(section.body) ? [] : [`${path}: body is required`];
    case 'section.contact':
      return isBlank(section.label) ? [`${path}: label is required`] : [];
    case 'section.button':
      if (isBlank(section.label)) return [`${path}: label is required`];
      return validateAction(section.action, path);
    case 'section.multi-content':
      return (section.blocks ?? []).flatMap((b: AnyRecord, i: number) => validateBlock(b, `${path}.blocks[${i}]`));
    case 'section.multi-page':
      if (!section.pages?.length) return [`${path}: multi-page needs at least one page`];
      return section.pages.flatMap((p: AnyRecord, pi: number) =>
        (p.blocks ?? []).flatMap((b: AnyRecord, bi: number) => validateBlock(b, `${path}.pages[${pi}].blocks[${bi}]`))
      );
    default:
      return [];
  }
};

export const validateSections = (sections: unknown[] | null | undefined): string[] =>
  (sections ?? []).flatMap((s, i) => validateSection(s as AnyRecord, `sections[${i}]`));
