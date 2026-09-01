# KirbyCMS + Tailwind  base project

A Kirby CMS base built on [Kirby Plainkit](https://github.com/getkirby/plainkit) + Tailwind CSS v4, managed with Composer. This repo has no client-specific content — it's meant to be cloned as the starting point for new sites. See [Starting a new project from this base](#starting-a-new-project-from-this-base) below.

## Licensing

This base and the tools referenced from it are free to use during local development, but require a paid license once a site goes live:

- **[Kirby CMS](https://getkirby.com)** — free to develop with locally; a license per domain is required for a live/production site.
- **[tobimori/kirby-seo](https://www.andkindness.com/seo)** — SEO plugin, not installed in this base by default (add per project as needed); same model, license required at go-live.
- **[kirby.tools/content-translator](https://kirby.tools/content-translator)** — optional install offered by `setup-languages.sh` (see [Multi-language support](#multi-language-support)); free to test locally, "pay only when you are ready to go live."

Budget for these licenses before launching a client site built on this base.

## Requirements

- PHP 8.2+ with extensions: `mbstring`, `xml`, `gd`, `curl`, `zip`, `intl`
- [Composer](https://getcomposer.org)

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
cd adaltavoce
composer install
bun install
```

## Run locally

In two separate terminals:

```bash
# Terminal 1 — PHP dev server
composer start

# Terminal 2 — CSS/JS watch mode
bun run dev
```

Then open `http://localhost:8000` in your browser.

The Kirby Panel is available at `http://localhost:8000/panel` — you will be prompted to create an admin account on first visit.

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
4. **Multi-language (optional)** — run `./setup-languages.sh` and follow the prompts to enable Kirby's multi-language mode and pick which languages to install. Skip it and the site stays single-language, matching this repo as-is. See "Multi-language support" below.
5. **`README.md`** — replace this file's title/intro with the new project's name and description; delete this section and `CLAUDE.md`'s "Starting a new project from this base" pointer if you don't want them carried over (optional — harmless to leave).
6. Run `composer install && bun install` and start building pages, blueprints, and templates on top of `site/templates/default.php`.

Everything else — the Tailwind setup, header/footer snippets, `.gitignore`, and `deploy-example.sh` pattern — carries over as-is.

When this base itself improves (a new convention, a fixed gotcha, a better default snippet), consider whether the change belongs here so future clones benefit too.

## Site panel defaults

Every site built from this base needs the same chrome — a header CTA, company info, social links, and privacy/cookie policy links — so it's wired in by default instead of being rebuilt per project. It all lives in `site/blueprints/site.yml`, under Panel → Site Settings:

- **Header tab** — an icon/label/URL call-to-action button. Rendered by `site/snippets/cta-button.php`, called from `site/snippets/header.php` as the last item in the desktop nav and always visible next to the hamburger button on mobile (not tucked inside the collapsible menu). Renders nothing if the label or URL is empty.
- **Company information tab** — a site logo upload (shown in the header in place of the text title, once set), company name/address/phone/email, a repeatable social links structure (icon + label + URL), and page pickers for the privacy and cookie policy pages.
  - `content/privacy-policy/` and `content/cookie-policy/` are placeholder pages (unlisted, so they don't appear in the main nav) — replace their text with the real policies per project, and pick them in this tab's page fields so the footer links appear.
- `site/snippets/footer.php` and `header.php` render all of the above and degrade cleanly when a field is empty (e.g. no logo yet → falls back to the text title; no social links yet → nothing renders in that row).
- Icons (CTA and social links) use [`tobimori/kirby-icon-field`](https://github.com/tobimori/kirby-icon-field) (installed via Composer, `type: icon` in the blueprint), reading SVGs from `assets/icons/` (tracked in git, unlike `assets/css`/`assets/js`). A starter set of common platforms ships in that folder (Facebook, Instagram, X, LinkedIn, YouTube, TikTok, WhatsApp, Pinterest) — drop in more `.svg` files there as needed and they show up in the field's picker automatically.
  - The plugin caches its `assets/icons/` folder scan by default, keyed by the field's config (folder/sprite/include/exclude) rather than the folder's actual contents — so dropping in a new `.svg` won't show up in the Panel until that cache is cleared (delete `site/cache/<host>/tobimori/`) or invalidated some other way. `site/config/config.php` disables this cache (`'tobimori.icon-field' => ['cache' => false]`) so new icons always show up immediately — worth re-enabling (remove that config block) once a project's icon set has stabilized, since it does add a small perf cost on every Panel load of an icon field.

## Homepage hero section

The homepage still uses the regular `default.yml`/`default.php` — there's no dedicated home blueprint or template. The hero banner is instead a "Homepage hero section" field group inside `site/blueprints/site.yml`'s Header tab, rendered by `site/snippets/hero.php`, which `default.php` includes only when `$page->isHomePage()` is true. Every other page ignores these fields entirely.

- **Fields**: eyebrow, title, description, an overall hero text color, and a repeatable buttons structure (icon + label + URL + per-button background color + text color).
- **Layout toggle**: "Full width" vs "Contained" (`heroFullWidth`) — contained shows the hero as a rounded, inset card matching the page's `max-w-5xl` container; full width bleeds it edge-to-edge with square corners, flush against the header.
- **Background**: a radio picks Image or Solid color; the relevant fields (image upload, "add color overlay" toggle + overlay color, or background color) appear conditionally via blueprint `when:` — each condition is a single exact-value match, since Kirby's `when` only supports "and" logic natively (no plugin needed here).
- Color fields use Kirby's [color field](https://getkirby.com/docs/reference/panel/fields/color) with a shared set of swatches (a YAML anchor `&heroSwatches` at the top of `site.yml`) matching this base's default neutral palette — update those hex values once a project defines its own brand colors in `src/main.css`'s `@theme` block.
- Renders nothing if eyebrow/title/description/buttons are all empty — a fresh clone's homepage looks exactly like any other default page until the hero fields are filled in.
- Colors are applied via inline `style` attributes (not Tailwind classes) since they're arbitrary values chosen at runtime in the Panel, not known at Tailwind's build time. Each dynamic value is escaped once with `esc($value, 'attr')` (the attribute-embedding context) — escaping with `'css'` first and `'attr'` again double-encodes and corrupts the style string.

## Multi-language support

This base ships single-language by default, but is multi-language-ready: `site/snippets/language-switcher.php` renders nothing unless Kirby's multi-language mode is on, so it's already wired into `header.php` (desktop nav and mobile menu) with zero visual effect today.

To turn it on for a new project, run `./setup-languages.sh` right after cloning (before customizing `content/`). It will:

1. Ask which languages to install (a preset list of common ones, or custom `code:Name:locale` entries) and which is the default.
2. If more than one language was selected, ask whether to also set up [kirby.tools/content-translator](https://kirby.tools/content-translator) for one-click page translation in the Panel (see [Licensing](#licensing) — free locally, paid license required once the site goes live). If yes:
   - Runs `composer require johannschopplich/kirby-content-translator` (or prints the command if Composer isn't on `PATH` yet).
   - Asks which provider to use — DeepL, AI via Kirby Copilot (OpenAI), or skip and configure later.
   - Adds the provider config to `site/config/config.php`, reading the API key from an environment variable (`DEEPL_API_KEY` or `OPENAI_API_KEY`) rather than writing it into the file — `config.php` is committed to git, so the key itself must be set outside of it (shell env, host/server env config, etc).
   - Adds the `content-translator` button to `site/blueprints/pages/default.yml`'s `buttons:` list (skipped with a manual instruction if that file already defines `buttons:`).
   - Reminds you to activate a license in the Panel's System view before going live.
3. Create `site/config/config.php` with `'languages' => true` (plus the content-translator config from step 2, if set up) — skipped if the file already exists, in which case add the keys yourself.
4. Create one `site/languages/{code}.php` file per selected language.
5. Migrate `content/site.txt`, `content/home/home.txt`, and `content/error/error.txt` into per-language copies (e.g. `home.en.txt`, `home.es.txt`) — the non-default ones start as duplicates of the default and need translating via the Panel (or via content-translator, if installed).

Only run it once, on a fresh clone — it expects the un-suffixed content filenames to still exist.

## Frontend build

The frontend uses [Tailwind CSS v4](https://tailwindcss.com) via the `@tailwindcss/vite` plugin. Source files live in `src/` and compile to `assets/` (gitignored, rebuilt on every deploy).

```bash
bun run dev     # watch mode, rebuilds on changes to src/, templates, snippets
bun run build   # production build → assets/css/ and assets/js/
```

- `src/main.css` — Tailwind entry point, `@theme` customizations, custom CSS
- `src/main.js` — entry point for JS behavior (mobile menu, etc.)
- `site/snippets/header.php` / `site/snippets/footer.php` — shared page chrome, styled with Tailwind utility classes
- `site/templates/default.php` — default page template

## Deploying to a live server

A deploy script is included to push the site to a DreamHost VPS via SSH/rsync.

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

Also make sure Kirby's writable directories exist on the server (they are created automatically by the first deploy, but you can create them ahead of time):

```bash
mkdir -p ~/your-domain.com/site/cache ~/your-domain.com/site/sessions ~/your-domain.com/site/accounts
```

### Running a deploy

```bash
./deploy.sh
```

This will:
1. Run `bun run build` to compile CSS and JS
2. Upload all required files via rsync (only changed files are transferred)
3. Run `composer install` on the server to build `vendor/` and `kirby/`
4. Set correct write permissions on Kirby's data directories

### What is excluded from the upload

- `.git`, `.gitignore`, `README.md`, `node_modules/`, `src/`
- `vendor/`, `kirby/` — installed on the server via Composer
- `deploy.sh`, `deploy-example.sh`
- `site/accounts`, `site/sessions`, `site/cache`

### Before the first deploy

- Make sure PHP 8.2+ is installed on the server with extensions: `mbstring`, `gd`, `curl`, `zip`, `intl`
- For Nginx servers, add a rewrite rule to route all requests through `index.php` (Apache/DreamHost is handled automatically via Kirby's `.htaccess`)
- Point the domain's web root at the project directory in the DreamHost panel

## Project structure

```
content/        ← pages and uploaded files (includes privacy-policy/, cookie-policy/ placeholders)
src/            ← Tailwind CSS + JS source (compiles to assets/)
assets/icons/   ← social icon SVGs for the icon field (tracked in git)
site/
  blueprints/   ← Panel field definitions (site.yml has the Footer tab)
  config/       ← config.php (email, SMTP, plugin settings)
  plugins/      ← custom and third-party plugins (composer-managed ones are gitignored)
  templates/    ← PHP templates
  snippets/     ← reusable template partials (header, footer)
```

## Notes

- `vendor/` and `kirby/` are not committed — they are restored by `composer install`
- Never commit `site/accounts/`, `site/sessions/`, or `site/cache/`
- `deploy.sh` contains server credentials and is gitignored — never commit it
