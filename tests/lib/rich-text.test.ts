import { blocksToHtml, htmlToBlocks, type Blocks } from '../../src/lib/rich-text';

const t = (text: string, marks: Record<string, boolean> = {}) => ({ type: 'text', text, ...marks });

describe('htmlToBlocks', () => {
  it('turns a <div> with text into a paragraph without a tag', () => {
    expect(htmlToBlocks('<div>Hello</div>')).toEqual([{ type: 'paragraph', children: [t('Hello')] }]);
  });

  it('turns a <p> into a paragraph tagged "p"', () => {
    expect(htmlToBlocks('<p>Hello</p>')).toEqual([{ type: 'paragraph', tag: 'p', children: [t('Hello')] }]);
  });

  it('flattens a div-wrapped h1 and nested div wrappers', () => {
    expect(htmlToBlocks('<div><h1>Title</h1><div><div>Body</div></div></div>')).toEqual([
      { type: 'heading', level: 1, children: [t('Title')] },
      { type: 'paragraph', children: [t('Body')] },
    ]);
  });

  it('maps h1-h6 to heading levels', () => {
    expect(htmlToBlocks('<h2>a</h2><h6>b</h6>')).toEqual([
      { type: 'heading', level: 2, children: [t('a')] },
      { type: 'heading', level: 6, children: [t('b')] },
    ]);
  });

  it('converts ul/li and ol/li to lists', () => {
    expect(htmlToBlocks('<ul><li>a</li><li>b</li></ul><ol><li>c</li></ol>')).toEqual([
      {
        type: 'list',
        format: 'unordered',
        children: [
          { type: 'list-item', children: [t('a')] },
          { type: 'list-item', children: [t('b')] },
        ],
      },
      { type: 'list', format: 'ordered', children: [{ type: 'list-item', children: [t('c')] }] },
    ]);
  });

  it('uses the inline content of a div inside a li as the list-item text', () => {
    expect(htmlToBlocks('<ol><li><div><div>x</div></div></li><li><p>y</p></li></ol>')).toEqual([
      {
        type: 'list',
        format: 'ordered',
        children: [
          { type: 'list-item', children: [t('x')] },
          { type: 'list-item', children: [t('y')] },
        ],
      },
    ]);
  });

  it('keeps nested lists as child lists', () => {
    expect(htmlToBlocks('<ul><li>a<ul><li>b</li></ul></li></ul>')).toEqual([
      {
        type: 'list',
        format: 'unordered',
        children: [
          { type: 'list-item', children: [t('a')] },
          { type: 'list', format: 'unordered', indentLevel: 1, children: [{ type: 'list-item', children: [t('b')] }] },
        ],
      },
    ]);
  });

  it('marks strong/b text as bold and merges adjacent text', () => {
    expect(htmlToBlocks('<div>See <strong>Manage</strong> and <b>this</b></div>')).toEqual([
      { type: 'paragraph', children: [t('See '), t('Manage', { bold: true }), t(' and '), t('this', { bold: true })] },
    ]);
  });

  it('turns <br> into a newline inside the text', () => {
    expect(htmlToBlocks('<p>one <br /> two<br>three</p>')).toEqual([
      { type: 'paragraph', tag: 'p', children: [t('one \n two\nthree')] },
    ]);
  });

  it('decodes named and numeric HTML entities', () => {
    expect(htmlToBlocks('<p>&laquo;a&raquo; &ndash; it&rsquo;s&nbsp;&amp; &#65;&#x42; &quot;q&quot;</p>')).toEqual([
      { type: 'paragraph', tag: 'p', children: [t('«a» – it’s & AB "q"')] },
    ]);
  });

  it('drops empty paragraphs but keeps an &nbsp; spacer', () => {
    expect(htmlToBlocks('<div></div><p> </p><div>a</div><div>&nbsp;</div>')).toEqual([
      { type: 'paragraph', children: [t('a')] },
      { type: 'paragraph', children: [t(' ')] },
    ]);
  });

  it('groups loose inline content next to block children into paragraphs', () => {
    expect(htmlToBlocks('<div>intro<ul><li>x</li></ul>outro</div>')).toEqual([
      { type: 'paragraph', children: [t('intro')] },
      { type: 'list', format: 'unordered', children: [{ type: 'list-item', children: [t('x')] }] },
      { type: 'paragraph', children: [t('outro')] },
    ]);
  });

  it('keeps stray text that looks like a broken tag as text', () => {
    expect(htmlToBlocks('<div>end./div></div>')).toEqual([{ type: 'paragraph', children: [t('end./div>')] }]);
  });

  it('parses other inline formatting and links', () => {
    expect(htmlToBlocks('<p><em>i</em><u>u</u><s>s</s><code>c</code><a href="https://a.org">l <strong>b</strong></a></p>')).toEqual([
      {
        type: 'paragraph',
        tag: 'p',
        children: [
          t('i', { italic: true }),
          t('u', { underline: true }),
          t('s', { strikethrough: true }),
          t('c', { code: true }),
          { type: 'link', url: 'https://a.org', children: [t('l '), t('b', { bold: true })] },
        ],
      },
    ]);
  });

  it('returns an empty array for empty input', () => {
    expect(htmlToBlocks('')).toEqual([]);
  });
});

describe('blocksToHtml', () => {
  it('renders paragraphs as <div> by default and <p> when tagged', () => {
    expect(blocksToHtml([{ type: 'paragraph', children: [t('a')] }, { type: 'paragraph', tag: 'p', children: [t('b')] }] as Blocks)).toBe(
      '<div>a</div><p>b</p>'
    );
  });

  it('renders headings, lists and nested lists', () => {
    const blocks = [
      { type: 'heading', level: 3, children: [t('H')] },
      {
        type: 'list',
        format: 'ordered',
        children: [
          { type: 'list-item', children: [t('a')] },
          { type: 'list', format: 'unordered', indentLevel: 1, children: [{ type: 'list-item', children: [t('b')] }] },
          { type: 'list-item', children: [t('c')] },
        ],
      },
    ] as Blocks;
    expect(blocksToHtml(blocks)).toBe('<h3>H</h3><ol><li>a<ul><li>b</li></ul></li><li>c</li></ol>');
  });

  it('renders every inline mark, newlines and links', () => {
    const blocks = [
      {
        type: 'paragraph',
        children: [
          t('b', { bold: true }),
          t('i', { italic: true }),
          t('u', { underline: true }),
          t('s', { strikethrough: true }),
          t('c', { code: true }),
          t('x\ny'),
          { type: 'link', url: 'https://a.org/?q="1"&r=2', children: [t('go')] },
        ],
      },
    ] as Blocks;
    expect(blocksToHtml(blocks)).toBe(
      '<div><strong>b</strong><em>i</em><u>u</u><s>s</s><code>c</code>x<br>y<a href="https://a.org/?q=&quot;1&quot;&amp;r=2">go</a></div>'
    );
  });

  it('renders quote, code and image blocks', () => {
    const blocks = [
      { type: 'quote', children: [t('q')] },
      { type: 'code', children: [t('a < b\nc')] },
      { type: 'image', image: { url: 'https://cdn/x.png', alternativeText: 'An "x"' }, children: [t('')] },
    ] as Blocks;
    expect(blocksToHtml(blocks)).toBe(
      '<blockquote>q</blockquote><pre><code>a &lt; b\nc</code></pre><img src="https://cdn/x.png" alt="An &quot;x&quot;">'
    );
  });

  it('escapes & < > " in text', () => {
    expect(blocksToHtml([{ type: 'paragraph', children: [t('a & <b> "c"')] }] as Blocks)).toBe(
      '<div>a &amp; &lt;b&gt; &quot;c&quot;</div>'
    );
  });

  it('returns an empty string for null/empty blocks', () => {
    expect(blocksToHtml(null)).toBe('');
    expect(blocksToHtml([])).toBe('');
  });
});

describe('round trip', () => {
  const roundTrip = (html: string) => blocksToHtml(htmlToBlocks(html));

  it('keeps <p> as <p> and <div> as <div>', () => {
    expect(roundTrip('<p>a</p>')).toBe('<p>a</p>');
    expect(roundTrip('<div>a</div>')).toBe('<div>a</div>');
  });

  it('keeps a mix of <p> and <div> in order', () => {
    expect(roundTrip('<p>1</p><div>2</div><p>3</p><div><div>4</div></div>')).toBe('<p>1</p><div>2</div><p>3</p><div>4</div>');
  });

  // Real samples from s3-content/content/resources/RO/ro/learn.json.
  const SAMPLES = [
    '<h1>Mindfulness Coach</h1><div><div>Mindfulness Coach oferă exerciții și informații care pot ajuta la:</div><ol><li><div>Concentrarea asupra momentului prezent</div></li><li><div><div>Elimină puterea gândurilor sau amintirilor nedorite</div></div></li></ol><div><div>Apasă pe pictograma de mesaj de mai jos pentru a partaja informații despre aceste aplicații cu alte persoane.</div></div></div>',
    '<div><div>Acest lucru poate fi cu adevărat înfricoșător. Consultă câteva dintre instrumentele de adaptare a furiei din această aplicație, accesând secțiunea <strong>Gestionare simptome</strong>.</div></div>',
    '<div><div>De exemplu pot apărea idei precum "nicăieri nu este sigur," sau "nimeni nu poate fi de încredere."</div><div>&nbsp;</div><div><div><div>Pentru majoritatea, aceste reacții se vor diminua în timp.</div></div></div></div>',
    '<div><h1>PTSD este tratabil</h1><div><div>Indiferent de cât timp ai trăit cu PTSD, există tratamente care te pot ajuta.</div></div></div>',
    '<div>Persoanele cu PTSD dezvoltă frecvent și alte tipuri de probleme. Acestea includ:</div><ul><li>depresie</li><li>atacuri de panică</li></ul><div><div>Știați că?:</div><ul><li><div>Mai mult de jumătate dintre bărbații cu PTSD au probleme cu alcoolul</div></li><li><div>Aproape jumătate dintre femeile cu PTSD au depresie</div></li></ul></div>',
    '<p>Ծնողի արձագանքը, <br /> երեխաները կարող են՝</p><ul><li>Feel confused about what&rsquo;s happening</li></ul>',
  ];

  it.each(SAMPLES.map((s, i) => [i, s]))('is stable for legacy sample %i', (_i, html) => {
    const once = roundTrip(html as string);
    expect(roundTrip(once)).toBe(once);
  });

});
