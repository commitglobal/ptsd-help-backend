import { randomUUID } from 'node:crypto';

type Data = Record<string, unknown> | undefined;

const isBlank = (value: unknown) => value === undefined || value === null || String(value).trim() === '';

/**
 * Editors don't have to fill `uid` (the id the mobile app uses for categories/topics):
 * a blank uid gets a new UUID on create, and a blank uid on update is dropped so the existing one is kept.
 */
export const withGeneratedUid = (action: string, data: Data, makeId: () => string = randomUUID): Data => {
  if (!data) return data;

  if (action === 'create' && isBlank(data.uid)) {
    return { ...data, uid: makeId() };
  }

  if (action === 'update' && 'uid' in data && isBlank(data.uid)) {
    const { uid: _blank, ...rest } = data;
    return rest;
  }

  return data;
};
