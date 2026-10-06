import { validateSections } from '../../src/validation/sections';

describe('validateSections', () => {
  it('accepts valid sections', () => {
    expect(
      validateSections([
        { __component: 'section.image', image: 12, alt: 'x' },
        { __component: 'section.rich-text', body: [{ type: 'paragraph', children: [{ type: 'text', text: 'x' }] }] },
        { __component: 'section.button', label: 'Go', action: { type: 'start_tool', toolId: 'rid' } },
        { __component: 'section.button', label: 'Call', action: { type: 'phone', number: '112' } },
        { __component: 'section.button', label: 'Site', action: { type: 'external', url: 'https://a.org' } },
        {
          __component: 'section.multi-page',
          pages: [
            {
              blocks: [
                { type: 'rich-text', body: [{ type: 'heading', level: 1, children: [{ type: 'text', text: 'a' }] }] },
                { type: 'text', content: 'plain' },
              ],
            },
          ],
        },
      ])
    ).toEqual([]);
  });

  it('accepts empty or missing sections', () => {
    expect(validateSections(undefined)).toEqual([]);
    expect(validateSections([])).toEqual([]);
  });

  it('rejects start_tool with an unknown tool id', () => {
    const errors = validateSections([
      { __component: 'section.button', label: 'Go', action: { type: 'start_tool', toolId: 'not-a-tool' } },
    ]);
    expect(errors).toEqual(['sections[0].action: unknown toolId "not-a-tool"']);
  });

  it('rejects actions missing their required field', () => {
    expect(
      validateSections([
        { __component: 'section.button', label: 'a', action: { type: 'external', url: 'ftp://x' } },
        { __component: 'section.button', label: 'b', action: { type: 'phone', number: '' } },
        { __component: 'section.button', label: 'c', action: { type: 'share' } },
      ])
    ).toEqual([
      'sections[0].action: url must start with http://, https:// or mailto:',
      'sections[1].action: number is required',
      'sections[2].action: message is required',
    ]);
  });

  it('rejects blocks missing fields required by their type', () => {
    expect(
      validateSections([
        {
          __component: 'section.multi-content',
          blocks: [
            { type: 'image' },
            { type: 'button', label: 'x' },
            { type: 'rich-text', body: [] },
            { type: 'rich-text', content: '<p>legacy html only</p>' },
            { type: 'text', content: '' },
          ],
        },
      ])
    ).toEqual([
      'sections[0].blocks[0]: image is required',
      'sections[0].blocks[1]: action is required',
      'sections[0].blocks[2]: body is required',
      'sections[0].blocks[3]: body is required',
      'sections[0].blocks[4]: content is required',
    ]);
  });

  it('rejects a rich-text section without a non-empty body', () => {
    expect(
      validateSections([
        { __component: 'section.rich-text', body: [] },
        { __component: 'section.rich-text', body: null },
        { __component: 'section.rich-text', content: '<p>x</p>' },
        { __component: 'section.text', content: '' },
      ])
    ).toEqual([
      'sections[0]: body is required',
      'sections[1]: body is required',
      'sections[2]: body is required',
      'sections[3]: content is required',
    ]);
  });

  it('rejects a multi-page without pages', () => {
    expect(validateSections([{ __component: 'section.multi-page', pages: [] }])).toEqual([
      'sections[0]: multi-page needs at least one page',
    ]);
  });
});
