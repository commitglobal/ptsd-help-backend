import { canonicalizeRichText } from '../../scripts/lib/canonical-rich-text';

describe('canonicalizeRichText', () => {
  it('rewrites every rich-text content through htmlToBlocks/blocksToHtml, leaving everything else alone', () => {
    const legacy = {
      title: 'Learn',
      pages: [
        {
          content: {
            sections: [
              { type: 'rich-text', content: '<div><h1>T</h1><div><div>a&nbsp;b</div></div></div>' },
              { type: 'text', content: '<div>plain</div>' },
              { type: 'multiContent', contentArray: [{ type: 'rich-text', content: '<p>x<br />y</p>' }] },
              { type: 'multiPage', pageArray: [[{ type: 'rich-text', content: '<ul><li><div>z</div></li></ul>' }]] },
            ],
          },
        },
      ],
    };
    expect(canonicalizeRichText(legacy)).toEqual({
      title: 'Learn',
      pages: [
        {
          content: {
            sections: [
              { type: 'rich-text', content: '<h1>T</h1><div>a b</div>' },
              { type: 'text', content: '<div>plain</div>' },
              { type: 'multiContent', contentArray: [{ type: 'rich-text', content: '<p>x<br>y</p>' }] },
              { type: 'multiPage', pageArray: [[{ type: 'rich-text', content: '<ul><li>z</li></ul>' }]] },
            ],
          },
        },
      ],
    });
  });
});
