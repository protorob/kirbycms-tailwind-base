# KirbyCMS + Tailwind  base project

A Kirby CMS base built on [Kirby Plainkit](https://github.com/getkirby/plainkit) + Tailwind CSS v4, managed with Composer. This repo has no client-specific content — it's meant to be cloned as the starting point for new sites. See [Starting a new project from this base](#starting-a-new-project-from-this-base) below.

## Licensing

This base and the tools referenced from it are free to use during local development, but require a paid license once a site goes live:

- **[Kirby CMS](https://getkirby.com)** — free to develop with locally; a license per domain is required for a live/production site.
- **[tobimori/kirby-seo](https://www.andkindness.com/seo)** — SEO plugin, not installed in this base by default (add per project as needed); same model, license required at go-live.
- **[kirby.tools/content-translator](https://kirby.tools/content-translator)** — planned addition to `setup-languages.sh` (see [Multi-language support](#multi-language-support)); free to test locally, "pay only when you are ready to go live."

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

## Multi-language support

This base ships single-language by default, but is multi-language-ready: `site/snippets/language-switcher.php` renders nothing unless Kirby's multi-language mode is on, so it's already wired into `header.php` (desktop nav and mobile menu) with zero visual effect today.

To turn it on for a new project, run `./setup-languages.sh` right after cloning (before customizing `content/`). It will:

1. Ask which languages to install (a preset list of common ones, or custom `code:Name:locale` entries) and which is the default.
2. Create `site/config/config.php` with `'languages' => true` (skipped if the file already exists — add the key yourself in that case).
3. Create one `site/languages/{code}.php` file per selected language.
4. Migrate `content/site.txt`, `content/home/home.txt`, and `content/error/error.txt` into per-language copies (e.g. `home.en.txt`, `home.es.txt`) — the non-default ones start as duplicates of the default and need translating via the Panel.

Only run it once, on a fresh clone — it expects the un-suffixed content filenames to still exist.

Planned: an option to also install [kirby.tools/content-translator](https://kirby.tools/content-translator) for one-click page translation in the Panel instead of manual copy/paste. See [Licensing](#licensing) — free locally, paid license required once the site goes live.

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
content/        ← pages and uploaded files
src/            ← Tailwind CSS + JS source (compiles to assets/)
site/
  blueprints/   ← Panel field definitions
  config/       ← config.php (email, SMTP, plugin settings)
  plugins/      ← custom and third-party plugins
  templates/    ← PHP templates
  snippets/     ← reusable template partials (header, footer)
```

## Notes

- `vendor/` and `kirby/` are not committed — they are restored by `composer install`
- Never commit `site/accounts/`, `site/sessions/`, or `site/cache/`
- `deploy.sh` contains server credentials and is gitignored — never commit it
