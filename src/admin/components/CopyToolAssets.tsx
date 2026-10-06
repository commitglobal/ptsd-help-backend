import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useFetchClient, useNotification } from '@strapi/strapi/admin';
import { Button, Field, Flex, Modal, SingleSelect, SingleSelectOption, Typography } from '@strapi/design-system';

const TOOL_ASSET_UID = 'api::tool-asset.tool-asset';

type CountryOption = { code: string; name: string; languages: string[] };
type CopyResult = { created: number; addedLocale: number; skipped: number; failed: { key: string; message: string }[] };

/** "RO|en" ↔ { country: 'RO', locale: 'en' }: one select per side keeps the dialog short. */
const toValue = (country: string, locale: string) => `${country}|${locale}`;
const fromValue = (value: string) => {
  const [country, locale] = value.split('|');
  return { country, locale };
};

const CopyDialog = () => {
  const { get, post } = useFetchClient();
  const { toggleNotification } = useNotification();
  const [open, setOpen] = useState(false);
  const [options, setOptions] = useState<CountryOption[]>([]);
  const [from, setFrom] = useState('');
  const [to, setTo] = useState('');
  const [busy, setBusy] = useState(false);
  const [result, setResult] = useState<CopyResult | null>(null);

  useEffect(() => {
    if (!open) return;
    setResult(null);
    get<{ countries: CountryOption[] }>('/ptsd-admin/tool-assets/copy-options')
      .then(({ data }) => setOptions(data.countries))
      .catch(() => toggleNotification({ type: 'danger', message: 'Nu am putut încărca lista de țări.' }));
  }, [open]);

  const pairs = options.flatMap(({ code, name, languages }) =>
    languages.map((locale) => ({ value: toValue(code, locale), label: `${name} – ${locale}` }))
  );

  const submit = async () => {
    const source = fromValue(from);
    const destination = fromValue(to);
    setBusy(true);
    try {
      const { data } = await post<CopyResult>('/ptsd-admin/tool-assets/copy', {
        fromCountry: source.country,
        fromLocale: source.locale,
        toCountry: destination.country,
        toLocale: destination.locale,
      });
      setResult(data);
      toggleNotification({
        type: data.failed.length ? 'warning' : 'success',
        message: `${data.created} create, ${data.addedLocale} adăugate pe limbă, ${data.skipped} sărite`,
      });
    } catch (error: any) {
      const message = error?.response?.data?.error?.message ?? 'Copierea a eșuat.';
      toggleNotification({ type: 'danger', message });
    } finally {
      setBusy(false);
    }
  };

  return (
    <Modal.Root open={open} onOpenChange={setOpen}>
      <Modal.Trigger>
        <Button variant="secondary">Copiază din altă țară/limbă</Button>
      </Modal.Trigger>
      <Modal.Content>
        <Modal.Header>
          <Modal.Title>Copiază Tool asset-uri</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Flex direction="column" alignItems="stretch" gap={4}>
            <Typography variant="omega">
              Pentru fiecare cheie a sursei: dacă destinația nu o are, se creează; dacă o are fără limba
              destinație, i se adaugă limba. Cheile care există deja pe limba destinație nu se modifică. Fișierele
              nu se duplică: intrările noi folosesc aceleași fișiere ca sursa, iar apoi le înlocuiești doar pe cele
              care diferă.
            </Typography>
            <Field.Root>
              <Field.Label>Sursă</Field.Label>
              <SingleSelect value={from} onChange={(value) => setFrom(String(value))} placeholder="Țară – limbă">
                {pairs.map((pair) => (
                  <SingleSelectOption key={pair.value} value={pair.value}>
                    {pair.label}
                  </SingleSelectOption>
                ))}
              </SingleSelect>
            </Field.Root>
            <Field.Root>
              <Field.Label>Destinație</Field.Label>
              <SingleSelect value={to} onChange={(value) => setTo(String(value))} placeholder="Țară – limbă">
                {pairs
                  .filter((pair) => pair.value !== from)
                  .map((pair) => (
                    <SingleSelectOption key={pair.value} value={pair.value}>
                      {pair.label}
                    </SingleSelectOption>
                  ))}
              </SingleSelect>
            </Field.Root>
            {result && (
              <Typography variant="omega">
                Rezultat: {result.created} create, {result.addedLocale} adăugate pe limbă, {result.skipped} sărite
                {result.failed.length > 0 &&
                  `, ${result.failed.length} eșuate: ${result.failed.map((f) => `${f.key} (${f.message})`).join('; ')}`}
              </Typography>
            )}
          </Flex>
        </Modal.Body>
        <Modal.Footer>
          <Modal.Close>
            <Button variant="tertiary">Închide</Button>
          </Modal.Close>
          <Button onClick={submit} loading={busy} disabled={!from || !to || busy}>
            Copiază
          </Button>
        </Modal.Footer>
      </Modal.Content>
    </Modal.Root>
  );
};

/** Shown in Content Manager → Tool asset list only (the injection zone is shared by every collection type). */
export const CopyToolAssets = () => {
  const { slug } = useParams<{ slug: string }>();
  return slug === TOOL_ASSET_UID ? <CopyDialog /> : null;
};
