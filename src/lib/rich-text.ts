/**
 * Conversion between HTML strings (what the mobile app renders) and Strapi "Rich text (Blocks)" JSON
 * (what editors write in the admin). Pure and dependency-free: no Strapi imports.
 *
 * Paragraphs render as <div> (the app's legacy look). A paragraph imported from a legacy <p> carries
 * `tag: 'p'` so it renders as <p> again; paragraphs created in the editor have no tag and render as <div>.
 */

export type TextNode = {
  type: 'text';
  text: string;
  bold?: boolean;
  italic?: boolean;
  underline?: boolean;
  strikethrough?: boolean;
  code?: boolean;
};
export type LinkNode = { type: 'link'; url: string; children: TextNode[] };
export type InlineNode = TextNode | LinkNode;

export type ParagraphBlock = { type: 'paragraph'; tag?: 'p'; children: InlineNode[] };
export type HeadingBlock = { type: 'heading'; level: 1 | 2 | 3 | 4 | 5 | 6; children: InlineNode[] };
export type ListItemNode = { type: 'list-item'; children: InlineNode[] };
export type ListBlock = {
  type: 'list';
  format: 'ordered' | 'unordered';
  indentLevel?: number;
  children: (ListItemNode | ListBlock)[];
};
export type QuoteBlock = { type: 'quote'; children: InlineNode[] };
export type CodeBlock = { type: 'code'; language?: string; children: TextNode[] };
export type ImageBlock = {
  type: 'image';
  image: { url: string; alternativeText?: string | null; [key: string]: unknown };
  children: InlineNode[];
};
export type Block = ParagraphBlock | HeadingBlock | ListBlock | QuoteBlock | CodeBlock | ImageBlock;
export type Blocks = Block[];

type Marks = Omit<TextNode, 'type' | 'text'>;
const MARK_KEYS = ['bold', 'italic', 'underline', 'strikethrough', 'code'] as const;

/* ------------------------------------------------------------------------------------------------
 * HTML -> DOM (tiny tolerant tokenizer)
 * ---------------------------------------------------------------------------------------------- */

type ElementNode = { kind: 'element'; tag: string; attrs: Record<string, string>; children: DomNode[] };
type DomTextNode = { kind: 'text'; text: string };
type DomNode = ElementNode | DomTextNode;

const VOID_TAGS = new Set(['br', 'img', 'hr', 'input', 'meta', 'link', 'wbr', 'area', 'base', 'col', 'embed', 'source', 'track']);
const BLOCK_TAGS = new Set([
  'div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote', 'pre', 'img', 'hr',
  'section', 'article', 'header', 'footer', 'main', 'aside', 'nav', 'figure', 'figcaption', 'table', 'body', 'html',
]);

const NAMED_ENTITIES: Record<string, string> = {
  amp: '&', lt: '<', gt: '>', quot: '"', apos: "'", nbsp: ' ',
  laquo: '«', raquo: '»', lsquo: '‘', rsquo: '’', sbquo: '‚',
  ldquo: '“', rdquo: '”', bdquo: '„', ndash: '–', mdash: '—',
  hellip: '…', bull: '•', middot: '·', copy: '©', reg: '®', trade: '™',
  deg: '°', euro: '€', times: '×', divide: '÷', shy: '­', prime: '′',
  iexcl: '¡', iquest: '¿', sect: '§', para: '¶', ensp: ' ',
  emsp: ' ', thinsp: ' ', zwnj: '‌', zwj: '‍', lrm: '‎', rlm: '‏',
};

export const decodeEntities = (text: string): string =>
  text.replace(/&(#x[0-9a-f]+|#[0-9]+|[a-z][a-z0-9]*);/gi, (match, body: string) => {
    if (body[0] === '#') {
      const code = body[1] === 'x' || body[1] === 'X' ? parseInt(body.slice(2), 16) : parseInt(body.slice(1), 10);
      return Number.isFinite(code) && code > 0 && code <= 0x10ffff ? String.fromCodePoint(code) : match;
    }
    return NAMED_ENTITIES[body] ?? NAMED_ENTITIES[body.toLowerCase()] ?? match;
  });

const parseAttrs = (source: string): Record<string, string> => {
  const attrs: Record<string, string> = {};
  const re = /([^\s=/>]+)(?:\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+)))?/g;
  let m: RegExpExecArray | null;
  while ((m = re.exec(source))) attrs[m[1].toLowerCase()] = decodeEntities(m[2] ?? m[3] ?? m[4] ?? '');
  return attrs;
};

const parseHtml = (html: string): ElementNode => {
  const root: ElementNode = { kind: 'element', tag: '#root', attrs: {}, children: [] };
  const stack: ElementNode[] = [root];
  const current = () => stack[stack.length - 1];
  const closeTo = (tag: string) => {
    for (let i = stack.length - 1; i > 0; i--) {
      if (stack[i].tag === tag) {
        stack.length = i;
        return;
      }
    }
  };

  const tagRe = /<!--[\s\S]*?-->|<(\/?)([a-zA-Z][a-zA-Z0-9]*)((?:[^>"']|"[^"]*"|'[^']*')*)>/g;
  let last = 0;
  let m: RegExpExecArray | null;
  while ((m = tagRe.exec(html))) {
    if (m.index > last) current().children.push({ kind: 'text', text: decodeEntities(html.slice(last, m.index)) });
    last = tagRe.lastIndex;
    if (m[0].startsWith('<!--')) continue;

    const closing = m[1] === '/';
    const tag = m[2].toLowerCase();
    if (closing) {
      closeTo(tag);
      continue;
    }

    // Implicit end tags, as browsers do: a new <li> closes an open <li>; a block element closes an open <p>.
    if (tag === 'li' && stack.some((e) => e.tag === 'li')) {
      const liIndex = stack.map((e) => e.tag).lastIndexOf('li');
      if (!stack.slice(liIndex + 1).some((e) => e.tag === 'ul' || e.tag === 'ol')) stack.length = liIndex;
    }
    if (BLOCK_TAGS.has(tag) && current().tag === 'p') stack.pop();

    const el: ElementNode = { kind: 'element', tag, attrs: parseAttrs(m[3]), children: [] };
    current().children.push(el);
    const selfClosing = /\/\s*$/.test(m[3]);
    if (!VOID_TAGS.has(tag) && !selfClosing) stack.push(el);
  }
  if (last < html.length) current().children.push({ kind: 'text', text: decodeEntities(html.slice(last)) });
  return root;
};

/* ------------------------------------------------------------------------------------------------
 * DOM -> Blocks
 * ---------------------------------------------------------------------------------------------- */

const MARK_TAGS: Record<string, keyof Marks> = {
  strong: 'bold', b: 'bold', em: 'italic', i: 'italic', u: 'underline', ins: 'underline',
  s: 'strikethrough', strike: 'strikethrough', del: 'strikethrough', code: 'code',
};

const isBlockElement = (node: DomNode): node is ElementNode => node.kind === 'element' && BLOCK_TAGS.has(node.tag);

const sameMarks = (a: TextNode, b: TextNode) => MARK_KEYS.every((k) => Boolean(a[k]) === Boolean(b[k]));

const makeText = (text: string, marks: Marks): TextNode => {
  const node: TextNode = { type: 'text', text };
  for (const key of MARK_KEYS) if (marks[key]) node[key] = true;
  return node;
};

/** Merges adjacent text nodes with identical marks and drops empty text nodes. */
const tidyInline = <T extends InlineNode>(nodes: T[]): T[] => {
  const out: T[] = [];
  for (const node of nodes) {
    if (node.type === 'text') {
      if (node.text === '') continue;
      const prev = out[out.length - 1];
      if (prev && prev.type === 'text' && sameMarks(prev, node)) {
        (prev as TextNode).text += node.text;
        continue;
      }
      out.push({ ...node });
    } else {
      const children = tidyInline(node.children);
      out.push({ ...node, children: children.length ? children : [makeText('', {})] } as T);
    }
  }
  return out;
};

/** Collects inline nodes of a subtree. Block elements inside (e.g. a <div> inside <li>) are separated by "\n". */
const collectInline = (nodes: DomNode[], marks: Marks, out: InlineNode[], pre = false): void => {
  let sawBlock = false;
  const hasContentBefore = () => out.length > 0 && inlineText(out) !== '';
  for (const node of nodes) {
    if (node.kind === 'text') {
      const text = pre ? node.text : node.text.replace(/[\t\r\n\f]/g, ' ');
      if (sawBlock && text.trim() !== '' && hasContentBefore()) out.push(makeText('\n', marks));
      if (sawBlock && text.trim() === '') continue;
      out.push(makeText(text, marks));
      sawBlock = false;
      continue;
    }
    if (node.tag === 'br') {
      out.push(makeText('\n', marks));
      continue;
    }
    if (node.tag === 'img' || node.tag === 'hr') continue;
    if (node.tag === 'a') {
      const linkChildren: InlineNode[] = [];
      collectInline(node.children, marks, linkChildren, pre);
      out.push({ type: 'link', url: node.attrs.href ?? '', children: linkChildren.filter((n): n is TextNode => n.type === 'text') });
      continue;
    }
    if (BLOCK_TAGS.has(node.tag)) {
      const inner: InlineNode[] = [];
      collectInline(node.children, marks, inner, pre || node.tag === 'pre');
      if (inlineText(inner).trim() === '' && !inlineText(inner).includes(' ')) continue;
      if (hasContentBefore() && !inlineText(out).endsWith('\n')) out.push(makeText('\n', marks));
      out.push(...inner);
      sawBlock = true;
      continue;
    }
    const mark = MARK_TAGS[node.tag];
    collectInline(node.children, mark ? { ...marks, [mark]: true } : marks, out, pre);
  }
};

const inlineText = (nodes: InlineNode[]): string =>
  nodes.map((n) => (n.type === 'text' ? n.text : n.children.map((c) => c.text).join(''))).join('');

/** A paragraph is empty when it only holds source whitespace (a <br> newline or &nbsp; counts as content). */
const isEmptyInline = (nodes: InlineNode[]) => /^[ ]*$/.test(inlineText(nodes));

const inlineOf = (el: ElementNode, pre = false): InlineNode[] => {
  const out: InlineNode[] = [];
  collectInline(el.children, {}, out, pre);
  return tidyInline(out);
};

const convertList = (el: ElementNode, indentLevel: number): ListBlock => {
  const list: ListBlock = { type: 'list', format: el.tag === 'ol' ? 'ordered' : 'unordered', children: [] };
  if (indentLevel > 0) list.indentLevel = indentLevel;

  for (const child of el.children) {
    if (child.kind === 'text') {
      if (child.text.trim() !== '') list.children.push({ type: 'list-item', children: tidyInline([makeText(child.text, {})]) });
      continue;
    }
    if (child.tag === 'ul' || child.tag === 'ol') {
      list.children.push(convertList(child, indentLevel + 1));
      continue;
    }
    // <li> (or any stray element treated as one): inline content, nested lists become child lists after it.
    const nested = child.children.filter((c): c is ElementNode => c.kind === 'element' && (c.tag === 'ul' || c.tag === 'ol'));
    const inlineSource: ElementNode = { ...child, children: child.children.filter((c) => !nested.includes(c as ElementNode)) };
    const inline = inlineOf(inlineSource);
    list.children.push({ type: 'list-item', children: nonEmpty(inline) });
    for (const n of nested) list.children.push(convertList(n, indentLevel + 1));
  }
  return list;
};

/** Strapi's editor expects at least one (possibly empty) text child. */
const nonEmpty = (nodes: InlineNode[]): InlineNode[] => (nodes.length ? nodes : [makeText('', {})]);

/**
 * Converts the children of a container (root, div, p, section...) into blocks. Runs of inline content become
 * paragraphs (tagged 'p' when the container is a <p>); block children are converted or flattened recursively.
 */
const convertContainer = (el: ElementNode, out: Blocks): void => {
  const tag = el.tag === 'p' ? 'p' : undefined;
  let run: DomNode[] = [];

  const flush = () => {
    if (!run.length) return;
    const children = inlineOf({ kind: 'element', tag: 'span', attrs: {}, children: run });
    run = [];
    if (isEmptyInline(children)) return;
    out.push(tag ? { type: 'paragraph', tag, children } : { type: 'paragraph', children });
  };

  for (const child of el.children) {
    if (!isBlockElement(child)) {
      run.push(child);
      continue;
    }
    flush();
    convertBlock(child, out);
  }
  flush();
};

const convertBlock = (el: ElementNode, out: Blocks): void => {
  const heading = /^h([1-6])$/.exec(el.tag);
  if (heading) {
    const children = inlineOf(el);
    if (!isEmptyInline(children)) out.push({ type: 'heading', level: Number(heading[1]) as HeadingBlock['level'], children });
    return;
  }
  switch (el.tag) {
    case 'ul':
    case 'ol': {
      const list = convertList(el, 0);
      if (list.children.length) out.push(list);
      return;
    }
    case 'li':
      out.push(convertList({ kind: 'element', tag: 'ul', attrs: {}, children: [el] }, 0));
      return;
    case 'blockquote': {
      const children = inlineOf(el);
      if (!isEmptyInline(children)) out.push({ type: 'quote', children });
      return;
    }
    case 'pre': {
      const text = inlineText(inlineOf(el, true));
      if (text !== '') out.push({ type: 'code', children: [makeText(text, {})] });
      return;
    }
    case 'img':
      if (el.attrs.src) {
        out.push({
          type: 'image',
          image: { url: el.attrs.src, alternativeText: el.attrs.alt ?? null },
          children: [makeText('', {})],
        });
      }
      return;
    case 'hr':
      return;
    default:
      convertContainer(el, out);
  }
};

export const htmlToBlocks = (html: string | null | undefined): Blocks => {
  const out: Blocks = [];
  if (!html) return out;
  convertContainer(parseHtml(html), out);
  return out;
};

/* ------------------------------------------------------------------------------------------------
 * Blocks -> HTML
 * ---------------------------------------------------------------------------------------------- */

export const escapeHtml = (text: string): string =>
  text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const renderText = (node: TextNode): string => {
  let html = escapeHtml(node.text ?? '').replace(/\n/g, '<br>');
  if (html === '') return '';
  if (node.code) html = `<code>${html}</code>`;
  if (node.strikethrough) html = `<s>${html}</s>`;
  if (node.underline) html = `<u>${html}</u>`;
  if (node.italic) html = `<em>${html}</em>`;
  if (node.bold) html = `<strong>${html}</strong>`;
  return html;
};

const renderInline = (nodes: InlineNode[] | undefined): string =>
  (nodes ?? [])
    .map((node) => {
      if (node.type === 'link') return `<a href="${escapeHtml(node.url ?? '')}">${renderInline(node.children)}</a>`;
      if (node.type === 'text') return renderText(node);
      return '';
    })
    .join('');

const renderList = (list: ListBlock): string => {
  const tag = list.format === 'ordered' ? 'ol' : 'ul';
  const items: string[] = [];
  for (const child of list.children ?? []) {
    if (child.type === 'list') {
      const nested = renderList(child);
      if (items.length) items[items.length - 1] += nested;
      else items.push(nested);
    } else if (child.type === 'list-item') {
      items.push(renderInline(child.children));
    }
  }
  return `<${tag}>${items.map((i) => `<li>${i}</li>`).join('')}</${tag}>`;
};

const renderBlock = (block: Block): string => {
  switch (block.type) {
    case 'paragraph': {
      const tag = block.tag === 'p' ? 'p' : 'div';
      return `<${tag}>${renderInline(block.children)}</${tag}>`;
    }
    case 'heading': {
      const level = Math.min(6, Math.max(1, Number(block.level) || 1));
      return `<h${level}>${renderInline(block.children)}</h${level}>`;
    }
    case 'list':
      return renderList(block);
    case 'quote':
      return `<blockquote>${renderInline(block.children)}</blockquote>`;
    case 'code':
      return `<pre><code>${escapeHtml((block.children ?? []).map((c) => c.text ?? '').join(''))}</code></pre>`;
    case 'image':
      return block.image?.url
        ? `<img src="${escapeHtml(block.image.url)}" alt="${escapeHtml(block.image.alternativeText ?? '')}">`
        : '';
    default:
      return '';
  }
};

export const blocksToHtml = (blocks: Blocks | null | undefined): string =>
  Array.isArray(blocks) ? blocks.map(renderBlock).join('') : '';
