import type { Schema, Struct } from '@strapi/strapi';

export interface ItemCategoryRef extends Struct.ComponentSchema {
  collectionName: 'components_item_category_refs';
  info: {
    displayName: 'Category';
  };
  attributes: {
    category: Schema.Attribute.Relation<'oneToOne', 'api::category.category'>;
  };
}

export interface ItemTopicRef extends Struct.ComponentSchema {
  collectionName: 'components_item_topic_refs';
  info: {
    displayName: 'Topic';
  };
  attributes: {
    topic: Schema.Attribute.Relation<'oneToOne', 'api::topic.topic'>;
  };
}

export interface SectionButton extends Struct.ComponentSchema {
  collectionName: 'components_section_buttons';
  info: {
    displayName: 'Button';
  };
  attributes: {
    action: Schema.Attribute.Component<'shared.action', false> &
      Schema.Attribute.Required;
    label: Schema.Attribute.String & Schema.Attribute.Required;
  };
}

export interface SectionContact extends Struct.ComponentSchema {
  collectionName: 'components_section_contacts';
  info: {
    displayName: 'Personal contacts';
  };
  attributes: {
    label: Schema.Attribute.String & Schema.Attribute.Required;
  };
}

export interface SectionImage extends Struct.ComponentSchema {
  collectionName: 'components_section_images';
  info: {
    displayName: 'Image';
  };
  attributes: {
    alt: Schema.Attribute.String;
    image: Schema.Attribute.Media<'images'> & Schema.Attribute.Required;
  };
}

export interface SectionMultiContent extends Struct.ComponentSchema {
  collectionName: 'components_section_multi_contents';
  info: {
    displayName: 'Multi content';
  };
  attributes: {
    blocks: Schema.Attribute.Component<'shared.block', true>;
  };
}

export interface SectionMultiPage extends Struct.ComponentSchema {
  collectionName: 'components_section_multi_pages';
  info: {
    displayName: 'Multi page';
  };
  attributes: {
    pages: Schema.Attribute.Component<'shared.page', true>;
  };
}

export interface SectionRichText extends Struct.ComponentSchema {
  collectionName: 'components_section_rich_texts';
  info: {
    displayName: 'Rich text';
  };
  attributes: {
    body: Schema.Attribute.Blocks & Schema.Attribute.Required;
  };
}

export interface SectionText extends Struct.ComponentSchema {
  collectionName: 'components_section_texts';
  info: {
    displayName: 'Text';
  };
  attributes: {
    content: Schema.Attribute.Text & Schema.Attribute.Required;
  };
}

export interface SharedAction extends Struct.ComponentSchema {
  collectionName: 'components_shared_actions';
  info: {
    displayName: 'Action';
  };
  attributes: {
    message: Schema.Attribute.Text;
    number: Schema.Attribute.String;
    toolId: Schema.Attribute.String;
    type: Schema.Attribute.Enumeration<
      ['start_tool', 'external', 'phone', 'webview', 'share']
    > &
      Schema.Attribute.Required;
    url: Schema.Attribute.String;
  };
}

export interface SharedBlock extends Struct.ComponentSchema {
  collectionName: 'components_shared_blocks';
  info: {
    description: 'Item inside multi-content / multi-page';
    displayName: 'Block';
  };
  attributes: {
    action: Schema.Attribute.Component<'shared.action', false>;
    alt: Schema.Attribute.String;
    body: Schema.Attribute.Blocks;
    content: Schema.Attribute.Text;
    image: Schema.Attribute.Media<'images'>;
    label: Schema.Attribute.String;
    type: Schema.Attribute.Enumeration<
      ['image', 'text', 'rich-text', 'button']
    > &
      Schema.Attribute.Required;
  };
}

export interface SharedPage extends Struct.ComponentSchema {
  collectionName: 'components_shared_pages';
  info: {
    displayName: 'Page';
  };
  attributes: {
    blocks: Schema.Attribute.Component<'shared.block', true>;
  };
}

export interface SharedSubtoolFlag extends Struct.ComponentSchema {
  collectionName: 'components_shared_subtool_flags';
  info: {
    displayName: 'Subtool flag';
  };
  attributes: {
    enabled: Schema.Attribute.Boolean &
      Schema.Attribute.Required &
      Schema.Attribute.DefaultTo<true>;
    key: Schema.Attribute.Enumeration<
      [
        'relationships-reconnect-with-partner',
        'relationships-positive-communication',
        'relationships-i-messages',
        'relationships-healthy-arguments',
        'mindfulness-conscious-breathing',
        'mindfulness-mindful-walking',
        'mindfulness-emotional-discomfort',
        'mindfulness-sense-awareness',
        'mindfulness-loving-kindness',
        'sleep-help',
        'sleep-habits',
        'sleep-perspective',
        'recreational-activities-alone',
        'recreational-activities-city',
        'recreational-activities-nature',
      ]
    > &
      Schema.Attribute.Required;
  };
}

export interface SharedToolFlag extends Struct.ComponentSchema {
  collectionName: 'components_shared_tool_flags';
  info: {
    displayName: 'Tool flag';
  };
  attributes: {
    enabled: Schema.Attribute.Boolean &
      Schema.Attribute.Required &
      Schema.Attribute.DefaultTo<true>;
    key: Schema.Attribute.Enumeration<
      [
        'relationships',
        'ambient-sounds',
        'mindfulness',
        'my-feelings',
        'worry-time',
        'rid',
        'sleep',
        'my-strengths',
        'recreational-activities',
        'soothe-senses',
        'connect-with-others',
        'change-perspective',
        'grounding',
        'quotes',
        'shift-thoughts',
      ]
    > &
      Schema.Attribute.Required;
    subcategories: Schema.Attribute.Component<'shared.subtool-flag', true>;
  };
}

declare module '@strapi/strapi' {
  export module Public {
    export interface ComponentSchemas {
      'item.category-ref': ItemCategoryRef;
      'item.topic-ref': ItemTopicRef;
      'section.button': SectionButton;
      'section.contact': SectionContact;
      'section.image': SectionImage;
      'section.multi-content': SectionMultiContent;
      'section.multi-page': SectionMultiPage;
      'section.rich-text': SectionRichText;
      'section.text': SectionText;
      'shared.action': SharedAction;
      'shared.block': SharedBlock;
      'shared.page': SharedPage;
      'shared.subtool-flag': SharedSubtoolFlag;
      'shared.tool-flag': SharedToolFlag;
    }
  }
}
