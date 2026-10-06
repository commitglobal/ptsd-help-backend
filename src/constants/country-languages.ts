/**
 * The country/language pairs the mobile app offers — keep in sync with
 * ptsd-help-mobile/constants/languages.ts (CountryLanguageMap). Content saved in any other
 * language for a country would never be requested by the app.
 */
export const COUNTRY_LANGUAGES: Record<string, readonly string[]> = {
  AM: ["hy"],
  RO: ["ro", "en", "uk"],
  UA: ["uk"],
  FR: ["fr"],
};

export const countryLanguageProblem = (
  country: string,
  locale: string,
): string | null => {
  const allowed = COUNTRY_LANGUAGES[country];
  if (!allowed)
    return `Țara ${country} nu are limbi configurate în CMS (src/constants/country-languages.ts).`;
  if (allowed.includes(locale)) return null;
  return `Limba "${locale}" nu este disponibilă pentru țara ${country}. Limbi permise: ${allowed.join(", ")}.`;
};
