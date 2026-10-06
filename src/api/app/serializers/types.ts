import type { Blocks } from '../../../lib/rich-text';

export type Media = { url: string; updatedAt?: string } | null | undefined;

export type ActionInput = {
  type: 'start_tool' | 'external' | 'phone' | 'webview' | 'share';
  toolId?: string | null;
  url?: string | null;
  number?: string | null;
  message?: string | null;
};

export type BlockInput = {
  type: 'image' | 'text' | 'rich-text' | 'button';
  image?: Media;
  alt?: string | null;
  /** Plain text, used when type = 'text'. */
  content?: string | null;
  /** Strapi blocks, used when type = 'rich-text'. */
  body?: Blocks | null;
  label?: string | null;
  action?: ActionInput | null;
};

export type SectionInput =
  | { __component: 'section.image'; image: Media; alt?: string | null }
  | { __component: 'section.text'; content: string }
  | { __component: 'section.rich-text'; body: Blocks | null }
  | { __component: 'section.button'; label: string; action: ActionInput }
  | { __component: 'section.contact'; label: string }
  | { __component: 'section.multi-content'; blocks: BlockInput[] }
  | { __component: 'section.multi-page'; pages: { blocks: BlockInput[] }[] };

type Timestamps = { updatedAt?: string | null };

export type TopicInput = Timestamps & {
  uid: string;
  label: string;
  icon: Media;
  sections: SectionInput[] | null;
};

export type CategoryInput = Timestamps & {
  uid: string;
  label: string;
  icon: Media;
  topics: TopicInput[] | null;
};

export type PageItemInput =
  | { __component: 'item.category-ref'; category: CategoryInput | null }
  | { __component: 'item.topic-ref'; topic: TopicInput | null };

export type ContentPageInput = Timestamps & {
  title: string;
  items: PageItemInput[] | null;
};

export type AssetInput = { key: string; file: Media };

export type FeatureFlagsInput = Timestamps & {
  tools: { key: string; enabled: boolean; subcategories?: { key: string; enabled: boolean }[] | null }[] | null;
};
