import type {
  ActionInput,
  BlockInput,
  CategoryInput,
  ContentPageInput,
  Media,
  SectionInput,
  TopicInput,
} from './types';
import { blocksToHtml } from '../../../lib/rich-text';

export const toAbsoluteUrl = (media: Media, baseUrl: string): string => {
  if (!media?.url) return '';
  if (/^https?:\/\//.test(media.url)) return media.url;
  return `${baseUrl.replace(/\/$/, '')}${media.url}`;
};

const serializeAction = (action: ActionInput) => {
  switch (action.type) {
    case 'start_tool':
      return { type: action.type, toolId: action.toolId ?? '' };
    case 'external':
    case 'webview':
      return { type: action.type, url: action.url ?? '' };
    case 'phone':
      return { type: action.type, number: action.number ?? '' };
    case 'share':
      return { type: action.type, message: action.message ?? '' };
    default:
      throw new Error(`Unknown action type: ${(action as ActionInput).type}`);
  }
};

const serializeBlock = (block: BlockInput, baseUrl: string) => {
  switch (block.type) {
    case 'image':
      return { type: 'image', src: toAbsoluteUrl(block.image, baseUrl), alt: block.alt ?? '' };
    case 'text':
      return { type: 'text', content: block.content ?? '' };
    case 'rich-text':
      return { type: 'rich-text', content: blocksToHtml(block.body) };
    case 'button':
      return { type: 'button', label: block.label ?? '', action: serializeAction(block.action!) };
    default:
      throw new Error(`Unknown block type: ${(block as BlockInput).type}`);
  }
};

const serializeSection = (section: SectionInput, baseUrl: string) => {
  switch (section.__component) {
    case 'section.image':
      return { type: 'image', src: toAbsoluteUrl(section.image, baseUrl), alt: section.alt ?? '' };
    case 'section.text':
      return { type: 'text', content: section.content };
    case 'section.rich-text':
      return { type: 'rich-text', content: blocksToHtml(section.body) };
    case 'section.button':
      return { type: 'button', label: section.label, action: serializeAction(section.action) };
    case 'section.contact':
      return { type: 'contact', label: section.label };
    case 'section.multi-content':
      return { type: 'multiContent', contentArray: (section.blocks ?? []).map((b) => serializeBlock(b, baseUrl)) };
    case 'section.multi-page':
      return {
        type: 'multiPage',
        pageArray: (section.pages ?? []).map((p) => (p.blocks ?? []).map((b) => serializeBlock(b, baseUrl))),
      };
    default:
      throw new Error(`Unknown section component: ${(section as SectionInput).__component}`);
  }
};

const serializeTopic = (topic: TopicInput, baseUrl: string, withType: boolean) => ({
  id: topic.uid,
  ...(withType ? { type: 'topic' } : {}),
  label: topic.label,
  icon: toAbsoluteUrl(topic.icon, baseUrl),
  content: { sections: (topic.sections ?? []).map((s) => serializeSection(s, baseUrl)) },
});

const serializeCategory = (category: CategoryInput, baseUrl: string) => ({
  id: category.uid,
  type: 'category',
  label: category.label,
  icon: toAbsoluteUrl(category.icon, baseUrl),
  topics: (category.topics ?? []).map((t) => serializeTopic(t, baseUrl, false)),
});

const latest = (dates: (string | null | undefined)[]): string =>
  dates
    .filter((d): d is string => Boolean(d))
    .reduce((max, d) => (new Date(d) > new Date(max) ? d : max), new Date(0).toISOString());

export const serializeContentPage = (page: ContentPageInput, baseUrl: string) => {
  const dates: (string | null | undefined)[] = [page.updatedAt];
  const pages: unknown[] = [];

  for (const item of page.items ?? []) {
    if (item.__component === 'item.category-ref' && item.category) {
      dates.push(item.category.updatedAt, ...(item.category.topics ?? []).map((t) => t.updatedAt));
      pages.push(serializeCategory(item.category, baseUrl));
    } else if (item.__component === 'item.topic-ref' && item.topic) {
      dates.push(item.topic.updatedAt);
      pages.push(serializeTopic(item.topic, baseUrl, true));
    }
  }

  return { lastUpdatedAt: latest(dates), title: page.title, pages };
};
