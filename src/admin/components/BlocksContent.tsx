import type { ReactNode } from 'react';
import { Box, Typography } from '@strapi/design-system';

/** Minimal renderer for Strapi "Rich text (Blocks)" JSON, styled with the admin design system. */

type TextNode = {
  type: 'text';
  text: string;
  bold?: boolean;
  italic?: boolean;
  underline?: boolean;
  strikethrough?: boolean;
  code?: boolean;
};
type LinkNode = { type: 'link'; url: string; children: TextNode[] };
type InlineNode = TextNode | LinkNode;
type Block = {
  type: string;
  level?: number;
  format?: 'ordered' | 'unordered';
  image?: { url: string; alternativeText?: string | null };
  children?: any[];
};

const HEADING_VARIANTS = ['alpha', 'alpha', 'beta', 'delta', 'epsilon', 'omega', 'omega'] as const;

const renderText = (node: TextNode, key: number) => {
  let content: ReactNode = node.text;
  if (node.code) content = <code>{content}</code>;
  if (node.bold) content = <strong>{content}</strong>;
  if (node.italic) content = <em>{content}</em>;
  if (node.underline) content = <u>{content}</u>;
  if (node.strikethrough) content = <s>{content}</s>;
  return <span key={key}>{content}</span>;
};

const renderInline = (nodes: InlineNode[] = []) =>
  nodes.map((node, i) =>
    node.type === 'link' ? (
      <a key={i} href={node.url} target="_blank" rel="noreferrer">
        {node.children.map(renderText)}
      </a>
    ) : (
      renderText(node, i)
    )
  );

const renderList = (block: Block, key: number): ReactNode => {
  const Tag = block.format === 'ordered' ? 'ol' : 'ul';
  return (
    <Box key={key} tag={Tag} paddingLeft={6} marginBottom={3}>
      {block.children?.map((item: Block, i: number) =>
        item.type === 'list' ? (
          renderList(item, i)
        ) : (
          <li key={i}>
            <Typography>{renderInline(item.children)}</Typography>
          </li>
        )
      )}
    </Box>
  );
};

const isEmpty = (block: Block) => (block.children ?? []).every((child) => child.type === 'text' && child.text === '');

const renderBlock = (block: Block, key: number): ReactNode => {
  switch (block.type) {
    case 'heading':
      return (
        <Box key={key} paddingTop={4} paddingBottom={2}>
          <Typography tag={`h${block.level ?? 2}` as any} variant={HEADING_VARIANTS[block.level ?? 2]}>
            {renderInline(block.children)}
          </Typography>
        </Box>
      );
    case 'list':
      return renderList(block, key);
    case 'quote':
      return (
        <Box key={key} paddingLeft={4} marginBottom={3} borderColor="neutral300" borderStyle="solid" borderWidth="0 0 0 4px">
          <Typography textColor="neutral600">{renderInline(block.children)}</Typography>
        </Box>
      );
    case 'code':
      return (
        <Box key={key} tag="pre" padding={4} marginBottom={3} background="neutral100" hasRadius style={{ overflowX: 'auto' }}>
          <code>{block.children?.map((child: TextNode) => child.text).join('')}</code>
        </Box>
      );
    case 'image':
      return block.image ? (
        <Box key={key} marginBottom={3}>
          <img src={block.image.url} alt={block.image.alternativeText ?? ''} style={{ maxWidth: '100%' }} />
        </Box>
      ) : null;
    default:
      return (
        <Box key={key} marginBottom={3}>
          {/* An empty paragraph (Enter twice in the editor) keeps one line of height, like in the editor. */}
          <Typography tag="p">{isEmpty(block) ? <br /> : renderInline(block.children)}</Typography>
        </Box>
      );
  }
};

// pre-wrap keeps line breaks typed with Shift+Enter (stored as "\n" inside a text node) and leading spaces.
export const BlocksContent = ({ content }: { content?: Block[] | null }) => (
  <Box style={{ whiteSpace: 'pre-wrap' }}>{(content ?? []).map(renderBlock)}</Box>
);
