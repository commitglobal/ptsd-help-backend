const BLOCKS = { populate: { image: true, action: true } };

export const SECTIONS_POPULATE = {
  on: {
    'section.image': { populate: '*' },
    'section.text': { populate: '*' },
    'section.rich-text': { populate: '*' },
    'section.button': { populate: '*' },
    'section.contact': { populate: '*' },
    'section.multi-content': { populate: { blocks: BLOCKS } },
    'section.multi-page': { populate: { pages: { populate: { blocks: BLOCKS } } } },
  },
};

const TOPIC = { populate: { icon: true, sections: SECTIONS_POPULATE } };

export const CONTENT_PAGE_POPULATE = {
  items: {
    on: {
      'item.category-ref': { populate: { category: { populate: { icon: true, topics: TOPIC } } } },
      'item.topic-ref': { populate: { topic: TOPIC } },
    },
  },
};

export const FEATURE_FLAGS_POPULATE = { tools: { populate: { subcategories: true } } };
