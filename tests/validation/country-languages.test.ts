import { COUNTRY_LANGUAGES, countryLanguageProblem } from '../../src/constants/country-languages';

describe('COUNTRY_LANGUAGES', () => {
  it('matches the country/language pairs the mobile app offers', () => {
    expect(COUNTRY_LANGUAGES).toEqual({ AM: ['hy'], RO: ['ro', 'en', 'uk'], UA: ['uk'], FR: ['fr'] });
  });
});

describe('countryLanguageProblem', () => {
  it('accepts the languages of each country', () => {
    expect(countryLanguageProblem('AM', 'hy')).toBeNull();
    expect(countryLanguageProblem('RO', 'ro')).toBeNull();
    expect(countryLanguageProblem('RO', 'en')).toBeNull();
  });

  it('rejects a language the country does not offer, listing the allowed ones', () => {
    expect(countryLanguageProblem('AM', 'ro')).toBe(
      'Limba "ro" nu este disponibilă pentru țara AM. Limbi permise: hy.'
    );
    expect(countryLanguageProblem('RO', 'hy')).toBe(
      'Limba "hy" nu este disponibilă pentru țara RO. Limbi permise: ro, en, uk.'
    );
  });

  it('rejects a country without configured languages', () => {
    expect(countryLanguageProblem('DE', 'de')).toBe(
      'Țara DE nu are limbi configurate în CMS (src/constants/country-languages.ts).'
    );
  });
});
