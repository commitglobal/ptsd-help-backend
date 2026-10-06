import { withGeneratedUid } from '../../src/lib/uid';

const makeId = () => 'generated-id';

describe('withGeneratedUid', () => {
  it('generates a uid on create when it is missing or blank', () => {
    expect(withGeneratedUid('create', { label: 'A' }, makeId)).toEqual({ label: 'A', uid: 'generated-id' });
    expect(withGeneratedUid('create', { label: 'A', uid: '' }, makeId)).toEqual({ label: 'A', uid: 'generated-id' });
    expect(withGeneratedUid('create', { label: 'A', uid: '   ' }, makeId)).toEqual({ label: 'A', uid: 'generated-id' });
    expect(withGeneratedUid('create', { label: 'A', uid: null }, makeId)).toEqual({ label: 'A', uid: 'generated-id' });
  });

  it('keeps a uid given on create', () => {
    expect(withGeneratedUid('create', { uid: 'legacy-uuid' }, makeId)).toEqual({ uid: 'legacy-uuid' });
  });

  it('drops a blank uid on update so the existing one is kept', () => {
    expect(withGeneratedUid('update', { label: 'B', uid: '' }, makeId)).toEqual({ label: 'B' });
    expect(withGeneratedUid('update', { label: 'B', uid: null }, makeId)).toEqual({ label: 'B' });
  });

  it('leaves updates without uid, or with a real uid, untouched', () => {
    expect(withGeneratedUid('update', { label: 'B' }, makeId)).toEqual({ label: 'B' });
    expect(withGeneratedUid('update', { uid: 'x' }, makeId)).toEqual({ uid: 'x' });
  });

  it('ignores other actions and missing data', () => {
    expect(withGeneratedUid('delete', { uid: '' }, makeId)).toEqual({ uid: '' });
    expect(withGeneratedUid('create', undefined, makeId)).toBeUndefined();
  });

  it('uses crypto.randomUUID by default', () => {
    const { uid } = withGeneratedUid('create', {}) as { uid: string };
    expect(uid).toMatch(/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/);
  });
});
