/**
 * Rich text is stored as Strapi blocks and rendered back to HTML, so the API returns a canonical form of the
 * legacy HTML (wrapper <div>s flattened, entities decoded, <br /> -> <br>...). The parity check compares against
 * that same canonical form: every `rich-text` item's `content` goes through htmlToBlocks -> blocksToHtml.
 */
import { blocksToHtml, htmlToBlocks } from '../../src/lib/rich-text';

type Json = Record<string, unknown>;

export const canonicalizeRichText = (value: unknown): unknown => {
  if (Array.isArray(value)) return value.map(canonicalizeRichText);
  if (!value || typeof value !== 'object') return value;

  const out: Json = {};
  for (const [key, v] of Object.entries(value as Json)) out[key] = canonicalizeRichText(v);
  if (out.type === 'rich-text' && typeof out.content === 'string') out.content = blocksToHtml(htmlToBlocks(out.content));
  return out;
};
