import { serializeContentPage, toAbsoluteUrl } from '../../src/api/app/serializers/content';
import type { ContentPageInput, TopicInput } from '../../src/api/app/serializers/types';

const BASE = 'http://localhost:1337';

const topic = (overrides: Partial<TopicInput> = {}): TopicInput => ({
  uid: 't-1',
  label: 'What is PTSD?',
  icon: { url: '/uploads/icon.png' },
  sections: [],
  updatedAt: '2026-01-01T00:00:00.000Z',
  ...overrides,
});

describe('toAbsoluteUrl', () => {
  it('prefixes relative upload urls with the base url', () => {
    expect(toAbsoluteUrl({ url: '/uploads/a.png' }, BASE)).toBe('http://localhost:1337/uploads/a.png');
  });
  it('keeps absolute urls (S3 provider)', () => {
    expect(toAbsoluteUrl({ url: 'https://bucket.s3.amazonaws.com/a.png' }, BASE)).toBe('https://bucket.s3.amazonaws.com/a.png');
  });
  it('returns empty string for missing media', () => {
    expect(toAbsoluteUrl(null, BASE)).toBe('');
  });
});

describe('serializeContentPage', () => {
  it('serializes a top-level topic with type "topic" and all section kinds', () => {
    const page: ContentPageInput = {
      title: 'Support',
      updatedAt: '2026-01-01T00:00:00.000Z',
      items: [
        {
          __component: 'item.topic-ref',
          topic: topic({
            sections: [
              { __component: 'section.image', image: { url: '/uploads/x.png' }, alt: 'X' },
              { __component: 'section.text', content: 'plain' },
              { __component: 'section.rich-text', body: [{ type: 'paragraph', tag: 'p', children: [{ type: 'text', text: 'Hi' }] }] },
              { __component: 'section.button', label: 'Call', action: { type: 'phone', number: '112' } },
              { __component: 'section.contact', label: 'Personal contacts' },
              {
                __component: 'section.multi-content',
                blocks: [{ type: 'button', label: 'Go', action: { type: 'start_tool', toolId: 'rid' } }],
              },
              {
                __component: 'section.multi-page',
                pages: [
                  {
                    blocks: [
                      { type: 'rich-text', body: [{ type: 'heading', level: 1, children: [{ type: 'text', text: 'One' }] }] },
                      { type: 'text', content: 'plain <b>' },
                    ],
                  },
                  { blocks: [{ type: 'image', image: { url: '/uploads/y.png' }, alt: 'Y' }] },
                ],
              },
            ],
          }),
        },
      ],
    };

    expect(serializeContentPage(page, BASE).pages).toEqual([
      {
        id: 't-1',
        type: 'topic',
        label: 'What is PTSD?',
        icon: 'http://localhost:1337/uploads/icon.png',
        content: {
          sections: [
            { type: 'image', src: 'http://localhost:1337/uploads/x.png', alt: 'X' },
            { type: 'text', content: 'plain' },
            { type: 'rich-text', content: '<p>Hi</p>' },
            { type: 'button', label: 'Call', action: { type: 'phone', number: '112' } },
            { type: 'contact', label: 'Personal contacts' },
            { type: 'multiContent', contentArray: [{ type: 'button', label: 'Go', action: { type: 'start_tool', toolId: 'rid' } }] },
            {
              type: 'multiPage',
              pageArray: [
                [
                  { type: 'rich-text', content: '<h1>One</h1>' },
                  { type: 'text', content: 'plain <b>' },
                ],
                [{ type: 'image', src: 'http://localhost:1337/uploads/y.png', alt: 'Y' }],
              ],
            },
          ],
        },
      },
    ]);
  });

  it('serializes categories with nested topics that have no "type" field', () => {
    const page: ContentPageInput = {
      title: 'Learn',
      items: [
        {
          __component: 'item.category-ref',
          category: { uid: 'c-1', label: 'About PTSD', icon: { url: '/uploads/c.png' }, topics: [topic()] },
        },
      ],
    };

    expect(serializeContentPage(page, BASE).pages).toEqual([
      {
        id: 'c-1',
        type: 'category',
        label: 'About PTSD',
        icon: 'http://localhost:1337/uploads/c.png',
        topics: [{ id: 't-1', label: 'What is PTSD?', icon: 'http://localhost:1337/uploads/icon.png', content: { sections: [] } }],
      },
    ]);
  });

  it('serializes every action type with only its own field', () => {
    const page: ContentPageInput = {
      title: 'T',
      items: [
        {
          __component: 'item.topic-ref',
          topic: topic({
            sections: [
              { __component: 'section.button', label: 'a', action: { type: 'external', url: 'https://a.org', toolId: null } },
              { __component: 'section.button', label: 'b', action: { type: 'webview', url: 'https://b.org' } },
              { __component: 'section.button', label: 'c', action: { type: 'share', message: 'hello' } },
            ],
          }),
        },
      ],
    };
    const sections = (serializeContentPage(page, BASE).pages[0] as any).content.sections;
    expect(sections.map((s: any) => s.action)).toEqual([
      { type: 'external', url: 'https://a.org' },
      { type: 'webview', url: 'https://b.org' },
      { type: 'share', message: 'hello' },
    ]);
  });

  it('skips page items whose category/topic was deleted (null reference)', () => {
    const page: ContentPageInput = {
      title: 'Learn',
      items: [
        { __component: 'item.category-ref', category: null },
        { __component: 'item.topic-ref', topic: topic() },
      ],
    };
    expect(serializeContentPage(page, BASE).pages).toHaveLength(1);
  });

  it('renders an image section without media as empty src instead of crashing', () => {
    const page: ContentPageInput = {
      title: 'T',
      items: [{ __component: 'item.topic-ref', topic: topic({ sections: [{ __component: 'section.image', image: null, alt: null }] }) }],
    };
    const sections = (serializeContentPage(page, BASE).pages[0] as any).content.sections;
    expect(sections).toEqual([{ type: 'image', src: '', alt: '' }]);
  });

  it('renders rich-text without body as an empty string', () => {
    const page: ContentPageInput = {
      title: 'T',
      items: [
        {
          __component: 'item.topic-ref',
          topic: topic({
            sections: [
              { __component: 'section.rich-text', body: null },
              { __component: 'section.multi-content', blocks: [{ type: 'rich-text', body: null }] },
            ],
          }),
        },
      ],
    };
    const sections = (serializeContentPage(page, BASE).pages[0] as any).content.sections;
    expect(sections).toEqual([
      { type: 'rich-text', content: '' },
      { type: 'multiContent', contentArray: [{ type: 'rich-text', content: '' }] },
    ]);
  });

  it('uses the most recent updatedAt of page, categories and topics as lastUpdatedAt', () => {
    const page: ContentPageInput = {
      title: 'Learn',
      updatedAt: '2026-01-01T00:00:00.000Z',
      items: [
        {
          __component: 'item.category-ref',
          category: {
            uid: 'c-1',
            label: 'C',
            icon: null,
            updatedAt: '2026-02-01T00:00:00.000Z',
            topics: [topic({ updatedAt: '2026-03-01T00:00:00.000Z' })],
          },
        },
      ],
    };
    expect(serializeContentPage(page, BASE).lastUpdatedAt).toBe('2026-03-01T00:00:00.000Z');
  });
});
