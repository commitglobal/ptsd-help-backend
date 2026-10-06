# PTSD Help Backend (Strapi v5)

Backend de conținut pentru aplicația PTSD Help: Learn, Support, media pentru tool-uri și feature flags.
Design: `../docs/superpowers/specs/2026-09-28-strapi-cms-design.md`.

## Local

Dezvoltarea locală folosește PostgreSQL-ul local configurat în `.env` (implicit `127.0.0.1:5432`, baza
`ptsd-help-backend`). Fără Docker.

```bash
cp .env.example .env        # completează secretele și datele bazei locale
npm install
npm run develop             # http://localhost:1337/admin
```

## Import date existente (o singură dată)

```bash
node ../pull-s3-content.mjs   # actualizează ../s3-content
npm run build
npm run import:s3             # --reset pentru re-import
npm run develop
npm run parity                # compară cu JSON-urile S3
```

Importul aplică și două corecții pentru date legacy (vezi `scripts/lib/legacy-fixes.ts`: URL-uri externe/webview fără schemă și butoane fără label) și afișează avertismente `WARN:` pentru fiecare valoare corectată.

## Endpoint-uri pentru aplicație (publice, read-only)

| Endpoint | Înlocuiește |
|----------|-------------|
| `GET /api/app/config/foggles/:country` | `config/foggles_{C}.json` |
| `GET /api/app/config/tools-assets/:country/:locale` | `config/CMSToolsAssetsMapping_{C}_{l}.json` |
| `GET /api/app/content/:country/:locale/learn` | `content/resources/{C}/{l}/learn.json` |
| `GET /api/app/content/:country/:locale/support` | `content/resources/{C}/{l}/support.json` |

Draft & Publish e dezactivat pe toate tipurile: orice modificare salvată în admin apare imediat în endpoint-uri.

## Tools assets

Media pentru tool-uri și simptome se gestionează în **Content Manager → Tool asset**: o intrare per fișier, adică
per (țară, cheie). `key` (enumerare din `ASSET_KEYS`) și `country` sunt comune tuturor limbilor; `file` e
localizat, deci fiecare limbă a țării poate avea alt fișier (adaugă limba din selectorul de locale al intrării).
Lista se poate căuta și filtra după cheie și țară. O a doua intrare cu aceeași cheie în aceeași țară e refuzată la
salvare. `key` și `country` se aleg la Create și nu se mai pot schimba după (serverul refuză salvarea). `GET /api/app/config/tools-assets/:country/:locale` întoarce `{ [key]: { uri, lastUpdatedAt } }` din
toate intrările țării în limba cerută.

Pentru o țară sau o limbă nouă, intrările nu se creează de mână: butonul **„Copiază din altă țară/limbă”** din
lista Tool asset (`src/admin/components/CopyToolAssets.tsx`) copiază toate cheile unei țări/limbi existente, cu
aceleași fișiere din Media Library (fără upload nou). Cheile care lipsesc la destinație se creează, cele care există
fără limba destinație primesc limba, iar cele care o au deja nu se modifică. Butonul apelează
`POST /ptsd-admin/tool-assets/copy` (rută de admin, doar cu login de admin; `src/admin-api/tool-asset-copy.ts`).

Migrarea din vechiul tip „Tools assets” (o intrare per țară cu 81 de componente) s-a făcut o singură dată, cu
serverul oprit, cu `npm run build && npm run migrate:tool-asset` (idempotent; face întâi un `pg_dump` în
`../.superpowers/sdd/2026-09-28-strapi-cms/`).

## Țări și limbi

Limbile permise pe țară sunt în `src/constants/country-languages.ts` (AM → hy; RO → ro, en, uk; UA → uk) și trebuie
să fie identice cu `CountryLanguageMap` din `ptsd-help-mobile/constants/languages.ts`. La salvarea unei Content page
sau a unui Tool asset într-o limbă pe care țara nu o are, Strapi refuză cu un mesaj. La o țară sau limbă nouă se
actualizează ambele fișiere. Pașii compleți (mobil, backend, Strapi): `../docs/adaugare-tara-limba.docx`.

## Chei generate

`src/constants/keys.ts`, enumerarea `key` din `api::tool-asset.tool-asset` și componentele `shared.tool-flag`, `shared.subtool-flag` se generează din
`../s3-content` și `../ptsd-help-mobile/hooks/useTools.ts`:

```bash
npm run generate:keys
```

Rulează din nou când aplicația adaugă tool-uri sau chei de media.

## Rich text

Câmpurile rich text folosesc editorul Strapi **Rich text (Blocks)**: `section.rich-text.body` și `shared.block.body`
(pentru blocurile de tip `rich-text`; blocurile de tip `text` folosesc câmpul text simplu `content`). Endpoint-urile
aplicației trimit în continuare HTML: `src/lib/rich-text.ts` convertește blocurile în HTML (paragrafele devin `<div>`,
iar cele importate din `<p>` legacy rămân `<p>`).

Migrarea conținutului HTML existent în blocuri (o singură dată, cu serverul oprit; backup în `scripts/.richtext-backup.json`):

```bash
npm run build
npm run migrate:richtext
```

Scripturi de curățenie (o singură dată, idempotente, cu serverul oprit):

- `npx tsx scripts/cleanup-legacy-richtext-content.ts` golește HTML-ul legacy din `shared.block.content` la blocurile `rich-text` care au deja `body`.
- `npx tsx scripts/cleanup-orphan-components.ts [--apply]` găsește (dry run) și șterge (`--apply`, într-o tranzacție) rândurile de componente orfane rămase după update-uri.

## Roluri admin

După primul start, în **Settings → Administration panel → Roles → Editor**: debifează `Country` și `Feature flags`
(le gestionează doar Super Admin). Rolul **Public** (Users & Permissions) nu primește nicio permisiune.

## Staging (Docker)

Docker este doar pentru staging. `docker-compose.yml` pornește serviciul `strapi` (build din `Dockerfile`,
`env_file: .env`, port `1337:1337`, `restart: unless-stopped`). Baza de date vine din variabilele `DATABASE_*`
din `.env`-ul de staging și poate fi o bază externă/managed.

```bash
docker compose up -d --build                    # DB externă, din .env
docker compose --profile with-db up -d --build  # + serviciul postgres inclus (opțional)
docker compose logs -f strapi
docker compose down
```

`.env`-ul de staging (aceleași chei ca `.env.example`) trebuie să conțină:

- secrete reale, puternice, unice (`APP_KEYS`, `API_TOKEN_SALT`, `ADMIN_JWT_SECRET`, `TRANSFER_TOKEN_SALT`,
  `JWT_SECRET`, `ENCRYPTION_KEY`, `DATABASE_PASSWORD`) — niciodată valorile din `.env.example`;
- `NODE_ENV=production`;
- `PUBLIC_URL` = URL-ul public al serverului;
- `IS_PROXIED=true` dacă serverul rulează în spatele unui reverse proxy TLS (altfel `false`);
- `UPLOAD_PROVIDER=aws-s3` și variabilele `S3_*`;
- variabilele bazei de date: `DATABASE_CLIENT`, `DATABASE_HOST`, `DATABASE_PORT`, `DATABASE_NAME`,
  `DATABASE_USERNAME`, `DATABASE_PASSWORD`, `DATABASE_SSL`.

**Opțional, `--profile with-db`:** pentru un host de staging fără bază de date proprie. Pornește și serviciul
`postgres` (Postgres 16, date în volumul `pgdata`), expus doar pe `127.0.0.1:5434`. În acest caz setează în `.env`
`DATABASE_HOST=postgres` și `DATABASE_PORT=5432` (rețeaua internă Docker); `DATABASE_NAME`, `DATABASE_USERNAME`
și `DATABASE_PASSWORD` sunt folosite și pentru inițializarea bazei.
