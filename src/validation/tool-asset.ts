/**
 * Pure helpers for the Tool asset uniqueness rule: one document per (country, key).
 * The documents middleware in src/index.ts does the DB lookups around them.
 */

export type RelationRef = { documentId: string } | { id: number };

const refFromItem = (item: unknown): RelationRef | undefined => {
  if (typeof item === 'string') return item.trim() ? { documentId: item } : undefined;
  if (typeof item === 'number') return { id: item };
  if (item && typeof item === 'object') {
    const { documentId, id } = item as { documentId?: unknown; id?: unknown };
    if (typeof documentId === 'string' && documentId) return { documentId };
    if (typeof id === 'number' || (typeof id === 'string' && id)) return { id: Number(id) };
  }
  return undefined;
};

/**
 * Reads a manyToOne relation value as written to the document service:
 * a documentId string, a numeric id, `{ documentId }`, `{ id }`, `{ connect: [...] }`, `{ set: [...] }` or an array.
 * Returns the target ref, `null` when the value clears the relation, or `undefined` when it leaves it unchanged.
 */
export const extractRelationRef = (value: unknown): RelationRef | null | undefined => {
  if (value === undefined) return undefined;
  if (value === null) return null;
  if (Array.isArray(value)) return value.length ? refFromItem(value[0]) ?? null : null;
  if (typeof value !== 'object') return refFromItem(value);

  const { set, connect, disconnect } = value as { set?: unknown; connect?: unknown; disconnect?: unknown };
  if (Array.isArray(set)) return set.length ? refFromItem(set[0]) ?? null : null;
  if (Array.isArray(connect) && connect.length) return refFromItem(connect[connect.length - 1]) ?? null;
  if (Array.isArray(disconnect) && disconnect.length) return null;
  if (set !== undefined || connect !== undefined || disconnect !== undefined) return undefined;
  return refFromItem(value);
};

type Existing = { key?: string | null; country?: { id?: number; documentId?: string } | null } | null | undefined;

/** Works out the (key, country) a write ends up with: the data first, the existing document otherwise. */
export const resolveToolAssetTarget = (data: Record<string, unknown> | undefined, existing: Existing) => {
  const key = (typeof data?.key === 'string' && data.key) || existing?.key || null;
  const fromData = extractRelationRef(data?.country);
  const country = fromData !== undefined ? fromData : extractRelationRef(existing?.country ?? null) ?? null;
  return { key, country };
};

/** Another document (different documentId) with the same key and country is a conflict; other locales of the same one are not. */
export const findConflictingDocumentId = (currentDocumentId: string | undefined, sameKeyAndCountry: { documentId: string }[]) =>
  sameKeyAndCountry.find((doc) => doc.documentId !== currentDocumentId)?.documentId ?? null;

export const duplicateToolAssetMessage = (key: string, country: string) =>
  `Există deja un Tool asset pentru cheia ${key} în țara ${country}`;

const sameRef = (ref: RelationRef, existing: { id?: number; documentId?: string }) =>
  'documentId' in ref ? ref.documentId === existing.documentId : ref.id === existing.id;

/** Key and country are fixed after creation: the app looks files up by (country, key). */
export const immutableToolAssetProblem = (data: Record<string, unknown> | undefined, existing: Existing): string | null => {
  if (!existing) return null;
  const message = 'Cheia și țara unui Tool asset nu se pot schimba după creare. Creează o intrare nouă.';

  if (typeof data?.key === 'string' && data.key !== existing.key) return message;

  const countryRef = extractRelationRef(data?.country);
  if (countryRef === undefined) return null;
  if (countryRef === null || !existing.country || !sameRef(countryRef, existing.country)) return message;
  return null;
};
