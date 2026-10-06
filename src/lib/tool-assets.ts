type Entry = { key: string; updatedAt?: string | null };

const time = (entry: Entry) => (entry.updatedAt ? Date.parse(entry.updatedAt) || 0 : 0);

/**
 * One Tool asset per key: when an editor mistake leaves two entries with the same key,
 * keep the most recently updated one (first-seen order is kept) and report the duplicated keys.
 */
export const latestPerKey = <T extends Entry>(entries: T[]): { entries: T[]; duplicateKeys: string[] } => {
  const byKey = new Map<string, T>();
  const duplicateKeys = new Set<string>();
  for (const entry of entries) {
    const current = byKey.get(entry.key);
    if (!current) {
      byKey.set(entry.key, entry);
      continue;
    }
    duplicateKeys.add(entry.key);
    if (time(entry) > time(current)) byKey.set(entry.key, entry);
  }
  return { entries: [...byKey.values()], duplicateKeys: [...duplicateKeys] };
};
