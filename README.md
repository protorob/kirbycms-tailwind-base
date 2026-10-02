# KirbyCMS + Tailwind  base project

A Kirby CMS base built on [Kirby Plainkit](https://github.com/getkirby/plainkit) + Tailwind CSS v4, managed with Composer. This repo has no client-specific content — it's meant to be cloned as the starting point for new sites. See [Starting a new project from this base](#starting-a-new-project-from-this-base) below.

## Licensing

This base and the tools referenced from it are free to use during local development, but require a paid license once a site goes live:

- **[Kirby CMS](https://getkirby.com)** — free to develop with locally; a license per domain is required for a live/production site.
- **[johannschopplich/kirby-helpers](https://github.com/johannschopplich/kirby-helpers)** — installed; handles `.env` loading, SEO meta tags, `sitemap.xml` and `robots.txt`. MIT-licensed, free in production too.
- **[kirby.tools/content-translator](https://kirby.tools/content-translator)** — optional install offered by `setup-languages.sh` (see [Multi-language support](#multi-language-support)); free to test locally, "pay only when you are ready to go live."

Budget for these licenses before launching a client site built on this base.

## Requirements

- PHP 8.2+ with extensions: `mbstring`, `xml`, `gd`, `curl`, `zip`, `intl`
- [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) 20.19+ or 22.12+ (includes npm) — required by Vite

## Installing PHP (Ubuntu / WSL2)

```bash
sudo apt update && sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-gd php8.3-intl
```

Make `php` point to 8.3 if needed:

```bash
sudo update-alternatives --set php /usr/bin/php8.3
```

## Installing Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## Setup

```bash
git clone <repo-url>
cd <project-folder>
composer install
npm install
cp .env.example .env   # optional — only needed for API keys, see "Environment variables (.env)"
```

## Run locally

In two separate terminals:

```bash
# Terminal 1 — PHP dev server
composer start

# Terminal 2 — CSS/JS watch mode
npm run dev
```

Then open `http://localhost:8000` in your browser.

The Kirby Panel is available at `http://localhost:8000/panel` — you will be prompted to create an admin account on first visit.

Port 8000 already taken (e.g. by another site)? Set `PORT` to use a different one: `PORT=8001 composer start`, then open `http://localhost:8001`. This relies on shell variable expansion, so on Windows run it from WSL.

## Starting a new project from this base

To spin up a new client site from this template:

```bash
git clone <this-repo-url> new-project-name
cd new-project-name

# Detach from this repo's history and start fresh
rm -rf .git
git init
```

Then, before the first commit:

1. **`composer.json`** — update `name` (e.g. `clientname/site`) and `description`.
2. **`package.json`** — no changes needed unless you rename scripts.
3. **`site/blueprints/site.yml`** and the Panel's Site Settings — set the real site title once you log into the Panel.
4. **Multi-language (optional)** — run `./setup-languages.sh` and follow the prompts to enable Kirby's multi-language mode and pick which languages to install. Skip it and the site stays single-language, matching this repo as-is. See "Multi-language support" below, or the site's own *Going multilingual* page.
5. **Demo content** — the homepage hero/body copy and the `content/1_going-multilingual/` page describe this starter itself. Replace the homepage copy (Site Settings → Header for the hero, the Home page for the body) and delete the guide page once you no longer need it.
6. **`README.md`** — replace this file's title/intro with the new project's name and description; delete this section and `CLAUDE.md`'s "Starting a new project from this base" pointer if you don't want them carried over (optional — harmless to leave).
7. Run `composer install && npm install` and start building pages, blueprints, and templates on top of `site/templates/default.php`.

Everything else — the Tailwind setup, header/footer snippets, `.gitignore`, and `deploy-example.sh` pattern — carries over as-is.

When this base itself improves (a new convention, a fixed gotcha, a better default snippet), consider whether the change belongs here so future clones benefit too.

## Site panel defaults

Every site built from this base needs the same chrome — a header CTA, company info, social links, and privacy/cookie policy links — so it's wired in by default instead of being rebuilt per project. It all lives in `site/blueprints/site.yml`, under Panel → Site Settings:

- **Header tab** — an icon/label/URL call-to-action button. Rendered by `site/snippets/cta-button.php`, called from `site/snippets/header.php` as the last item in the desktop nav and always visible next to the hamburger button on mobile (not tucked inside the collapsible menu). Renders nothing if the label or URL is empty.
- **Company information tab** — a site logo upload (shown in the header in place of the text title, once set), company name/address/phone/email, and a repeatable social links structure (icon + label + URL).
  - **Legal pages** is a repeatable structure (one `page` picker per row), not a fixed pair of fields — add as many legal pages as a project needs (privacy policy, cookie policy, terms of service, imprint, GDPR, etc.), in whatever order they should appear in the footer. Each row just picks a page; the link text is that page's own title.
  - `content/legal/privacy/` and `content/legal/cookie-policy/` are placeholder pages, grouped under an unlisted `content/legal/` parent page (so none of them appear in the main nav). Add more legal pages as further children of `legal/`. Replace their text with the real policies per project, and add them as rows in this tab's Legal pages structure so the footer links appear.
- `site/snippets/footer.php` and `header.php` render all of the above and degrade cleanly when a field is empty (e.g. no logo yet → falls back to the text title; no social links yet → nothing renders in that row).
- Icons (CTA and social links) use [`tobimori/kirby-icon-field`](https://github.com/tobimori/kirby-icon-field) (installed via Composer, `type: icon` in the blueprint), reading SVGs from `assets/icons/` (tracked in git, unlike `assets/css`/`assets/js`). A starter set of common platforms ships in that folder (Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp, Pinterest) — drop in more `.svg` files there as needed and they show up in the field's picker automatically.
  - The plugin caches its `assets/icons/` folder scan by default, keyed by the field's config (folder/sprite/include/exclude) rather than the folder's actual contents — so dropping in a new `.svg` won't show up in the Panel until that cache is cleared (delete `site/cache/<host>/tobimori/`) or invalidated some other way. `site/config/config.php` disables this cache (`'tobimori.icon-field' => ['cache' => false]`) so new icons always show up immediately — worth re-enabling (remove that config block) once a project's icon set has stabilized, since it does add a small perf cost on every Panel load of an icon field.

### URL fields use Kirby's `link` field

`ctaUrl`, `heroButtons.url`, and `social.url` are all `type: link` (not `type: url`), restricted to `options: [url, email, tel, anchor]`. Kirby's plain `url` field only validates `http(s)://`/`ftp://` values, so it rejected `mailto:`/`tel:` links outright — the `link` field gives editors a type-aware picker (URL / Email / Tel / Anchor) and validates each type correctly. The stored value already comes back scheme-prefixed (`mailto:...`, `tel:...`, `https://...`, `#...`), so templates use it directly as `href` with no extra resolution step — just `esc($field, 'attr')` since it lands in an HTML attribute.

The `page`/`file` options are deliberately excluded here: those store an unresolved `page://uuid`/`file://uuid` reference rather than a ready-to-use href, which would need a small resolver added to `cta-button.php`/`hero.php`/`footer.php` before it's usable. Add them (and the resolver) if a future need for internal-page or file CTAs comes up.

## Navigation and breadcrumbs

- **Desktop nav** — `header.php` lists `$site->children()->listed()`. A page with listed children gets a CSS-only dropdown (shown on hover or keyboard focus via `group-focus-within`; `invisible` keeps the hidden links out of the tab order until then). One level deep only. `#site-header` is `relative z-40` so the dropdowns stay above `<main>`, whose fade-in animation creates its own stacking context.
- **Mobile menu** — there's no hover on touch screens, so children are always listed, indented under their parent.
- **Breadcrumbs** — `site/snippets/breadcrumbs.php` renders Home › Parent › Current on any nested page (it's skipped on top-level pages). It's an accessible `<nav aria-label>` with an ordered list, and it also outputs schema.org `BreadcrumbList` JSON-LD for search engines. Called from `default.php`.

## Hero sections

The hero banner's fields (eyebrow, title, description, buttons, background) live in one shared fragment, `site/blueprints/fields/hero.yml`, pulled into a blueprint via a `type: group` field with `extends: fields/hero`. Group fields splice their child fields into the parent form inline — no visual wrapper, no content nesting under the group's own name — so the same field names (`eyebrow`, `heroTitle`, `heroButtons`, etc.) are reused wherever the group appears. `site/snippets/hero.php` takes whichever model is passed in as `snippet('hero', ['model' => ...])` and reads all fields off that model, so the identical fragment works for both the site and any page.

- **Homepage** — `site/blueprints/site.yml`'s Header tab includes the group unconditionally (`hero: extends: fields/hero`). `default.php` renders it via `snippet('hero', ['model' => $site])` whenever `$page->isHomePage()`.
- **Any other page** — `site/blueprints/pages/default.yml` has a Hero tab with a `heroToggle` toggle field, then the same group with `when: heroToggle: true`. Kirby's `group` field type propagates a `when:` set on the group to every field inside it automatically, so the whole hero only shows once the toggle is on. `default.php` renders it via `snippet('hero', ['model' => $page])` when the toggle is on, and skips the plain `<h1>` page-title heading in that case (the hero's own title stands in for it, avoiding two `<h1>`s on one page). Any new page blueprint added later (services, portfolio, blog, etc.) should copy this Hero tab and toggle pattern.
- The background-image field queries `model.images` rather than a hardcoded `site.images`/`page.images` — `model` is a binding Kirby always provides pointing at whichever model a blueprint query runs against (`ModelWithContent::query()`), so the one fragment scopes correctly to the site's own files or a specific page's own files depending on where it's used.
- **Fields**: eyebrow, title, description, an overall hero text color, and a repeatable buttons structure (icon + label + link + per-button background color + text color). Button links use Kirby's `link` field restricted to `[url, email, tel, anchor]` — the stored value comes back scheme-prefixed (`mailto:...`, `tel:...`, `https://...`, `#...`), so `hero.php` uses it directly as `href` with `esc($field, 'attr')`. `page`/`file` options are excluded on purpose, since they store an unresolved `page://uuid`/`file://uuid` reference that would need a resolver first.
- **Layout toggle**: "Full width" vs "Contained" (`heroFullWidth`) — contained shows the hero as a rounded, inset card; full width bleeds it edge-to-edge with square corners, flush against the header.
- **Background**: a radio picks Image or Solid color; the relevant fields (image upload, "add color overlay" toggle + overlay color, or background color) appear conditionally via blueprint `when:` — each condition is a single exact-value match, since Kirby's `when` only supports "and" logic natively (no plugin needed here).
- Color fields use Kirby's [color field](https://getkirby.com/docs/reference/panel/fields/color) with a shared set of swatches (a YAML anchor `&heroSwatches` at the top of `fields/hero.yml`) matching this base's default neutral palette — update those hex values once a project defines its own brand colors in `src/main.css`'s `@theme` block.
- Renders nothing if eyebrow/title/description/buttons are all empty — a page with the toggle on but no hero content shows nothing extra (and no `<h1>` at all, so fill in at least a title).
- Colors are applied via inline `style` attributes (not Tailwind classes) since they're arbitrary values chosen at runtime in the Panel, not known at Tailwind's build time. Each dynamic value is escaped once with `esc($value, 'attr')` (the attribute-embedding context) — escaping with `'css'` first and `'attr'` again double-encodes and corrupts the style string.

## Multi-language support

This base ships single-language by default, but is multi-language-ready. Everything below renders nothing (or falls back to English) until Kirby's multi-language mode is on, so it has no visual effect today:

- **Desktop:** `site/snippets/language-switcher.php` is a dropdown pill next to the CTA button. Closed, it shows the current language code; open, it lists the full language names. Toggle/close logic (click outside, focus leaving, <kbd>Esc</kbd>) lives in `src/main.js`. It takes the same `$class` sizes as `cta-button.php`, so both pills match in height.
- **Mobile:** `site/snippets/language-list.php` is a plain row of language names at the bottom of the mobile menu. It isn't the dropdown because the menu's `overflow-hidden` (needed for its collapse animation) would clip it, and the menu is already open anyway.
- **`<head>`:** `<html lang>` follows the current language, and every page gets `<link rel="alternate" hreflang>` tags for its other language versions plus `x-default`.

The site itself has a *Going multilingual* page (`content/1_going-multilingual/`) that walks an editor/developer through the steps below, including DeepL and `.env`.

To turn it on for a new project, run `./setup-languages.sh` right after cloning (before customizing `content/`). It will:

1. Ask which languages to install (a preset list of common ones, or custom `code:Name:locale` entries) and which is the default.
2. If more than one language was selected, ask whether to also set up [kirby.tools/content-translator](https://kirby.tools/content-translator) for one-click page translation in the Panel (see [Licensing](#licensing) — free locally, paid license required once the site goes live). If yes:
   - Runs `composer require johannschopplich/kirby-content-translator` (or prints the command if Composer isn't on `PATH` yet).
   - Asks which provider to use — DeepL, AI via Kirby Copilot (OpenAI), or skip and configure later.
   - Prints the provider config block for `site/config/config.php`, reading the API key with `env('DEEPL_API_KEY')` or `env('OPENAI_API_KEY')` rather than writing it into the file — `config.php` is committed to git, so the key itself goes in `.env` (see [Environment variables](#environment-variables-env)).
   - Adds the `content-translator` button to `site/blueprints/pages/default.yml`'s `buttons:` list (skipped with a manual instruction if that file already defines `buttons:`).
   - Reminds you to activate a license in the Panel's System view before going live.
3. Print the lines to add to `site/config/config.php`: `'languages' => true` plus the content-translator config from step 2, if set up. **This base already ships a `config.php`, so the script never writes to it — add those lines inside its `return [ ... ]` array yourself.** (It only creates the file from scratch if it's missing.)
4. Create one `site/languages/{code}.php` file per selected language.
5. Migrate every un-suffixed `.txt` file under `content/` into per-language copies (e.g. `home.en.txt`, `home.es.txt`) — discovered dynamically at run time (`find content -name '*.txt'`), not a fixed list, so it covers whatever pages exist at the time: the base's own defaults (`site.txt`, `home/home.txt`, `error/error.txt`, and the `legal/` pages' `default.txt` files) plus anything you've added on top. The non-default language copies start as duplicates of the default and need translating via the Panel (or via content-translator, if installed).

**Only run it on a still-single-language site** — it's meant for right after cloning, before or after adding content, but before turning on multi-language mode some other way. Before touching anything, it checks for three signs that the site is already multi-language — `site/config/config.php` already has `'languages' => true`, `site/languages/` already has language files, or `content/` already has a language-suffixed file like `home.en.txt` — and aborts with no changes if any of them are true, since re-running it against an already-migrated site would silently orphan the existing per-language content files (the discovery step only picks up un-suffixed files, so a second run finds nothing to migrate — but the safety check exists in case that assumption is ever violated, e.g. an un-suffixed file added back manually after migration).

## Environment variables (.env)

Secrets such as API keys go in a `.env` file in the project root, **never** in `site/config/config.php`, which is tracked in git. `.env` is gitignored; `.env.example` is the committed template listing the keys:

```
# DEEPL_API_KEY=
# OPENAI_API_KEY=
```

Loading is handled by `kirby-helpers`: `config.php` calls `Env::load()` at the top (config files are read before plugins boot, so this explicit step is needed) and then reads values with `env('DEEPL_API_KEY')`. If `.env` doesn't exist, the site still works and `env()` returns `null`.

- **On the server:** both deploy scripts skip `.env`, so a local key never overwrites the server's. With the SSH deploy, create `.env` on the server by hand once (next to `index.php`); with the FTP deploy, use `--with-env`.
- **Web access:** on Apache, Kirby's `.htaccess` blocks requests for dotfiles, so `/.env` isn't publicly readable. nginx needs an equivalent rule (`location ~ /\. { deny all; }`). The local dev server (`composer start`) **does** serve it, so don't expose it beyond your machine.

## Default page content

`site/blueprints/pages/default.yml`'s `text` field is a `blocks` field (Kirby's visual block editor — text, heading, image, gallery, video, quote, list, table, line, markdown, code, plus the custom Child pages block below), not a plain textarea/KirbyText field. `site/templates/default.php` renders it with `$page->text()->toBlocks()->toHtml()`, wrapped in the same `.prose` container as before, so `@tailwindcss/typography` still styles whatever the blocks produce.

This is the field editors see on any page using the default blueprint — including the homepage's own body content below the hero (the hero itself is unrelated, driven by its own fields on `$site`, see "Hero sections" above).

The Content tab's sidebar also has two fields used when the page shows up as a card in a Child pages block:

- **Page excerpt** (`pageExcerpt`) — a short plain-text summary shown under the card title.
- **Card image** (`cardImage`) — the page's featured image, previewed at 6/4 in the Panel.

### Child pages block

A custom block: pick a page and it shows that page's listed subpages as a grid of cards.

- **In the Panel** the block appears as a compact bar summarizing its settings (e.g. "Child pages · Services · 3 columns"); clicking it opens its fields in the drawer. It deliberately doesn't use `preview: fields`, because inline the selected page is a link and clicking it would navigate away from the page being edited.
- **Columns:** 2, 3 or 4 (default 2). On phones the cards always stack in one column; 3 and 4 columns only kick in on wide screens (`lg:`, 1024px+), and tablets get 2.
- **Card title:** the subpage's title. **Card text:** its Page excerpt (nothing if empty).
- **Card image** (3:2), in this order: the subpage's Card image; otherwise its hero background image, but only if its hero is on and set to "Image" (otherwise those fields are hidden in the Panel, so a leftover image would be invisible to editors); otherwise no image. Served as cropped `srcset` sizes, lazy-loaded, with `alt=""` since the title is the link text.
- The whole card is one link, and the image zooms slightly on hover. The grid has `not-prose` so the `.prose` wrapper in `default.php` doesn't restyle it.
- **Files:** `site/blueprints/blocks/child-pages.yml` (Panel form), `site/snippets/blocks/child-pages.php` (markup), and the `blocks.fieldsets` list in `site/config/config.php`. That list makes the block available in every blocks field, but it replaces Kirby's default list, so it repeats the core blocks too. Add any future custom block there as well.

## SEO: meta tags, sitemap and robots.txt

Handled by `kirby-helpers`, configured in `site/config/config.php` under `'johannschopplich.helpers'`. (This replaces the earlier suggestion of `tobimori/kirby-seo`, which is more than a starter needs and paid at go-live.)

**Meta tags.** `header.php` outputs the canonical link, meta robots, meta description and Open Graph / Twitter Card tags via `$page->meta()`. Values come from each page's **SEO** tab:

| Field | Used for | If empty |
| --- | --- | --- |
| SEO title (`customTitle`) | `<title>` ("SEO title — Site title"), `og:title` | page title |
| Meta description (`description`) | search and social description (max 160 chars) | Site → SEO |
| Social sharing image (`thumbnail`) | `og:image` | Site → SEO |
| Search engines (`robots`) | `noindex` hides the page from results and from the sitemap | indexed |

- The field names are fixed by the plugin, so any new page blueprint should copy the SEO tab from `default.yml`.
- The error page is always `noindex`.

**`/sitemap.xml`** lists every published page (with language versions once multilingual), excluding the error page and anything set to `noindex`. To exclude a whole page type, add its template to `sitemap.exclude.templates` in `config.php`.

**`/robots.txt`** allows all crawlers and points to the sitemap.

## Frontend build

The frontend uses [Tailwind CSS v4](https://tailwindcss.com) via the `@tailwindcss/vite` plugin. Source files live in `src/` and compile to `assets/` (gitignored, rebuilt on every deploy).

```bash
npm run dev     # watch mode, rebuilds on changes to src/, templates, snippets
npm run build   # production build → assets/css/ and assets/js/
```

- `src/main.css` — Tailwind entry point, `@theme` customizations, custom CSS
- `src/main.js` — entry point for JS behavior (mobile menu, desktop language dropdown)
- `site/snippets/header.php` / `site/snippets/footer.php` — shared page chrome, styled with Tailwind utility classes
- `site/templates/default.php` — default page template

## Page transitions

Every navigation here is a normal full page load (Kirby renders server-side, there's no client-side router), so "page transitions" are done with CSS only — no JS, no new dependency:

- **Fallback fade-in** — `main { animation: page-fade-in .4s ease }` in `src/main.css` fades in only the `<main>` content on load. It's deliberately scoped to `<main>`, not `body`: animating the whole `<body>` would make the header/nav fade in and flash on every page change too. `#site-header` isn't part of this animation, so it renders immediately and never flashes.
- **Cross-document view transitions** — `@view-transition { navigation: auto; }` opts into the browser's native View Transitions API for same-origin navigations. Where supported, the browser cross-fades the whole old/new page automatically — a real fade-out-then-fade-in, not just the fade-in above. `#site-header` additionally gets `view-transition-name: site-header`, which tells the browser to treat it as a persistent element across the transition (matched by name between the outgoing and incoming page) instead of cross-fading it with everything else — since the header's markup is normally identical between pages, this reads as the header simply staying in place while only the content crossfades.
  - **Browser support**: Chromium browsers (Chrome/Edge) only, as of writing. Firefox and Safari don't recognize `@view-transition` yet and silently ignore it — those browsers just get the fallback fade-in above (nav still doesn't flash, but no fade-out).
- **Not done (yet)**: a JS page-transition library (e.g. [Swup](https://swup.js.org)) that intercepts internal link clicks, fetches the next page, and swaps only `<main>`'s content without a full reload — the header DOM node would never even reload. That would work identically in every browser (not just Chromium), but adds real complexity (handling back/forward navigation, re-running `main.js`'s mobile-menu logic after each swap, scroll restoration, updating `<title>`, etc.) that isn't justified yet. Revisit if cross-browser parity becomes a priority.

## Deploying to a live server

Two deploy scripts are included: `deploy-example.sh` pushes the site to a server with SSH access (e.g. a DreamHost VPS) via rsync and runs Composer there; `deploy-ftp-example.sh` is for hosts with FTP only (see [Deploying via FTP](#deploying-via-ftp-no-ssh)).

### First-time setup (local)

```bash
cp deploy-example.sh deploy.sh
chmod +x deploy.sh
```

Open `deploy.sh` and fill in your server details:

```bash
SSH_USER="your-user"
SSH_HOST="your-server.com"
REMOTE_PATH="/home/your-user/your-domain.com"
SSH_PORT=22
PHP_BIN="/usr/local/php83/bin/php"   # path to PHP on the server
COMPOSER_BIN="~/composer"            # path to Composer on the server
```

`deploy.sh` is gitignored — your credentials will never be committed.

### First-time setup (server)

`vendor/` and `kirby/` are never uploaded — Composer runs on the server after each deploy so dependencies are always built for the server's PHP version. You need Composer installed on the server once:

```bash
ssh your-user@your-server.com
curl -sS https://getcomposer.org/installer | php
mv composer.phar ~/composer
```

On **DreamHost** the default CLI `php` may differ from the web PHP version configured for the domain. Find the available binaries:

```bash
ls /usr/local/php*/bin/php
```

Then set `PHP_BIN` in `deploy.sh` to match the PHP version configured for the domain in the DreamHost panel (e.g. `/usr/local/php83/bin/php`).

Create `.env` with the real API keys in the site folder on the server (if the project uses any — see [Environment variables](#environment-variables-env)).

Also make sure Kirby's writable directories exist on the server (they are created automatically by the first deploy, but you can create them ahead of time):

```bash
mkdir -p ~/your-domain.com/site/cache ~/your-domain.com/site/sessions ~/your-domain.com/site/accounts
```

### Running a deploy

```bash
./deploy.sh
```

This will:
1. Run `npm run build` to compile CSS and JS
2. Upload all required files via rsync (only changed files are transferred)
3. Run `composer install` on the server to build `vendor/` and `kirby/`
4. Set correct write permissions on Kirby's data directories

### What is excluded from the upload

- `.git`, `.gitignore`, `README.md`, `CLAUDE.md`, `.claude/`, `node_modules/`, `src/`
- `vendor/`, `kirby/` — installed on the server via Composer
- every root-level `.sh` script (`/*.sh`) — `deploy.sh`/`deploy-ftp.sh` hold credentials and would otherwise be downloadable from the web root
- `.env`, `.env.example` — create `.env` on the server once instead
- `site/accounts`, `site/sessions`, `site/cache`

### Before the first deploy

- Make sure PHP 8.2+ is installed on the server with extensions: `mbstring`, `gd`, `curl`, `zip`, `intl`
- For Nginx servers, add a rewrite rule to route all requests through `index.php` (Apache/DreamHost is handled automatically via Kirby's `.htaccess`)
- Point the domain's web root at the project directory in the DreamHost panel
- Create `.env` on the server (by hand over SSH, or with `--with-env` via FTP)

### Deploying via FTP (no SSH)

For hosts with FTP access only, no SSH, rsync or Composer on the server. Requires `lftp` locally (`sudo apt install lftp` / `brew install lftp`).

```bash
cp deploy-ftp-example.sh deploy-ftp.sh
chmod +x deploy-ftp.sh
```

Fill in `FTP_USER`, `FTP_HOST`, `FTP_PORT`, `FTP_TLS`, `REMOTE_PATH` (the site root as seen from the FTP login, often not the absolute server path), and optionally `FTP_PASS` (leave empty to be prompted). `deploy-ftp.sh` is gitignored.

```bash
./deploy-ftp.sh                   # upload
./deploy-ftp.sh --dry-run         # preview what would be uploaded
./deploy-ftp.sh --with-env        # first deploy: also upload .env
./deploy-ftp.sh --with-accounts   # also upload site/accounts/
```

Unlike the SSH deploy, `vendor/`, `kirby/` and `site/plugins/` **are** uploaded, since Composer can't run on the server: the script runs `composer install --no-dev` locally first, so your local PHP must be compatible with the server's (see `php` in `composer.json`). Only files newer locally are uploaded (`--only-newer`), so content edited in the live Panel isn't overwritten by older local copies, and nothing is ever deleted on the server. Excluded: the same paths as the SSH deploy (except `vendor/`, `kirby/`, `site/plugins/`), plus `media/` (Kirby regenerates thumbnails on the server); `.env` unless `--with-env`, and `site/accounts/` unless `--with-accounts`.

## Project structure

```
content/        ← pages and uploaded files (includes legal/ placeholders and the 1_going-multilingual/ guide)
src/            ← Tailwind CSS + JS source (compiles to assets/)
assets/icons/   ← social icon SVGs for the icon field (tracked in git)
site/
  blueprints/
    blocks/     ← custom blocks (child-pages.yml)
    fields/     ← reusable field fragments (hero.yml), pulled in via `extends:`
    pages/      ← page blueprints (default.yml has the Content + Hero + SEO tabs)
    site.yml    ← Header/Company info/SEO tabs (site-wide hero, CTA, socials, legal pages, SEO defaults)
  config/       ← config.php (plugins, SEO, block list; secrets come from .env)
  plugins/      ← custom and third-party plugins (composer-managed ones are gitignored)
  templates/    ← PHP templates
  snippets/     ← reusable template partials (header, footer, hero, cta-button, breadcrumbs,
                  language-switcher, language-list, blocks/child-pages)
```

## Notes

- `vendor/` and `kirby/` are not committed — they are restored by `composer install`
- Never commit `site/accounts/`, `site/sessions/`, or `site/cache/`
- `deploy.sh`, `deploy-ftp.sh` and `.env` contain credentials and are gitignored — never commit them
