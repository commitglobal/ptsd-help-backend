import {
  extractRelationRef,
  resolveToolAssetTarget,
  findConflictingDocumentId,
  duplicateToolAssetMessage,
  immutableToolAssetProblem,
} from '../../src/validation/tool-asset';

describe('immutableToolAssetProblem', () => {
  const existing = { key: 'RID.CATEGORY_ICON', country: { id: 1, documentId: 'ro-doc' } };
  const message = 'Cheia și țara unui Tool asset nu se pot schimba după creare. Creează o intrare nouă.';

  it('allows creates (no existing document)', () => {
    expect(immutableToolAssetProblem({ key: 'A', country: 'x' }, null)).toBeNull();
  });

  it('allows updates that keep key and country (e.g. only the file, or a new locale)', () => {
    expect(immutableToolAssetProblem({ file: 5 }, existing)).toBeNull();
    expect(immutableToolAssetProblem({ key: 'RID.CATEGORY_ICON', country: { connect: [], disconnect: [] } }, existing)).toBeNull();
    expect(immutableToolAssetProblem({ country: 'ro-doc' }, existing)).toBeNull();
    expect(immutableToolAssetProblem({ country: { connect: [{ id: 1 }] } }, existing)).toBeNull();
  });

  it('rejects a different key', () => {
    expect(immutableToolAssetProblem({ key: 'QUOTES.CATEGORY_ICON' }, existing)).toBe(message);
  });

  it('rejects moving to another country or clearing it', () => {
    expect(immutableToolAssetProblem({ country: { connect: [{ documentId: 'am-doc' }] } }, existing)).toBe(message);
    expect(immutableToolAssetProblem({ country: { connect: [], disconnect: [{ id: 1 }] } }, existing)).toBe(message);
    expect(immutableToolAssetProblem({ country: null }, existing)).toBe(message);
  });
});

describe('extractRelationRef', () => {
  it('reads a documentId string or a numeric id', () => {
    expect(extractRelationRef('abc123')).toEqual({ documentId: 'abc123' });
    expect(extractRelationRef(7)).toEqual({ id: 7 });
  });

  it('reads { documentId } and { id } objects', () => {
    expect(extractRelationRef({ documentId: 'abc' })).toEqual({ documentId: 'abc' });
    expect(extractRelationRef({ id: 3 })).toEqual({ id: 3 });
    expect(extractRelationRef({ id: 3, documentId: 'abc', locale: null })).toEqual({ documentId: 'abc' });
  });

  it('reads the last entry of connect and the first of set', () => {
    expect(extractRelationRef({ connect: [{ id: 1, documentId: 'x' }], disconnect: [] })).toEqual({ documentId: 'x' });
    expect(extractRelationRef({ connect: ['a', 'b'] })).toEqual({ documentId: 'b' });
    expect(extractRelationRef({ set: [{ id: 9 }] })).toEqual({ id: 9 });
    expect(extractRelationRef({ set: ['s1'] })).toEqual({ documentId: 's1' });
  });

  it('reads a plain array shorthand', () => {
    expect(extractRelationRef(['a'])).toEqual({ documentId: 'a' });
  });

  it('returns null when the relation is cleared', () => {
    expect(extractRelationRef(null)).toBeNull();
    expect(extractRelationRef({ set: [] })).toBeNull();
    expect(extractRelationRef({ connect: [], disconnect: [{ id: 1 }] })).toBeNull();
    expect(extractRelationRef([])).toBeNull();
  });

  it('returns undefined when the value does not change the relation', () => {
    expect(extractRelationRef(undefined)).toBeUndefined();
    expect(extractRelationRef({ connect: [], disconnect: [] })).toBeUndefined();
    expect(extractRelationRef({})).toBeUndefined();
    expect(extractRelationRef('')).toBeUndefined();
  });
});

describe('resolveToolAssetTarget', () => {
  const existing = { key: 'RID.CATEGORY_ICON', country: { id: 1, documentId: 'ro' } };

  it('uses key and country from the data', () => {
    expect(resolveToolAssetTarget({ key: 'A', country: 'am' }, null)).toEqual({ key: 'A', country: { documentId: 'am' } });
  });

  it('falls back to the existing document when the data omits them', () => {
    expect(resolveToolAssetTarget({ file: 5 }, existing)).toEqual({ key: 'RID.CATEGORY_ICON', country: { documentId: 'ro' } });
    expect(resolveToolAssetTarget({ country: { connect: [], disconnect: [] } }, existing)).toEqual({
      key: 'RID.CATEGORY_ICON',
      country: { documentId: 'ro' },
    });
  });

  it('prefers the data over the existing document', () => {
    expect(resolveToolAssetTarget({ key: 'B', country: { connect: [{ id: 2 }] } }, existing)).toEqual({ key: 'B', country: { id: 2 } });
  });

  it('returns null country when the data clears it or nothing is known', () => {
    expect(resolveToolAssetTarget({ country: null }, existing).country).toBeNull();
    expect(resolveToolAssetTarget({ key: 'A' }, null)).toEqual({ key: 'A', country: null });
    expect(resolveToolAssetTarget(undefined, null)).toEqual({ key: null, country: null });
  });
});

describe('findConflictingDocumentId', () => {
  it('flags another document with the same key and country', () => {
    expect(findConflictingDocumentId('doc1', [{ documentId: 'doc2' }])).toBe('doc2');
    expect(findConflictingDocumentId(undefined, [{ documentId: 'doc2' }])).toBe('doc2');
  });

  it('allows the same document (another locale of it, or itself)', () => {
    expect(findConflictingDocumentId('doc1', [{ documentId: 'doc1' }, { documentId: 'doc1' }])).toBeNull();
    expect(findConflictingDocumentId('doc1', [])).toBeNull();
  });

  it('builds the Romanian error message', () => {
    expect(duplicateToolAssetMessage('RID.CATEGORY_ICON', 'RO')).toBe(
      'Există deja un Tool asset pentru cheia RID.CATEGORY_ICON în țara RO'
    );
  });
});
