/**
 * Pure planning for "copy Tool assets from one country/language to another".
 * The service in src/api/tool-asset-copy does the DB reads and writes around it.
 */

export type SourceAsset = { key: string; fileId: number | null };
export type DestinationDocument = { documentId: string; key: string; locales: string[] };

export type ToolAssetCopyPlan = {
  /** Keys the destination country does not have: a new document in the destination locale. */
  create: { key: string; fileId: number }[];
  /** Keys the destination country has, but not in the destination locale: a new locale on that document. */
  addLocale: { documentId: string; key: string; fileId: number }[];
  /** Keys already present in the destination locale: left untouched. */
  skipped: string[];
};

export const planToolAssetCopy = (
  source: SourceAsset[],
  destination: DestinationDocument[],
  destinationLocale: string
): ToolAssetCopyPlan => {
  const byKey = new Map(destination.map((doc) => [doc.key, doc]));
  const plan: ToolAssetCopyPlan = { create: [], addLocale: [], skipped: [] };
  const seen = new Set<string>();

  for (const { key, fileId } of source) {
    if (fileId === null || seen.has(key)) continue;
    seen.add(key);

    const existing = byKey.get(key);
    if (!existing) plan.create.push({ key, fileId });
    else if (existing.locales.includes(destinationLocale)) plan.skipped.push(key);
    else plan.addLocale.push({ documentId: existing.documentId, key, fileId });
  }
  return plan;
};
