# adaltavoce — Claude Code context

## What this project is

A Kirby CMS base project: [Plainkit](https://github.com/getkirby/plainkit) + Tailwind CSS v4, set up as a reusable starting point for new client sites. This particular repo is the template itself — it has no client-specific content beyond the Plainkit defaults (a `home` page and an `error` page).

When cloned to start a new project, add pages, blueprints, templates and content on top of this base. See "Starting a new project from this base" in the README.

## Tech stack

- **Kirby CMS 5** (Plainkit) — flat-file CMS, no database
- **Composer** — installs `kirby/` and `vendor/`, both gitignored and restored on `composer install`
- **Tailwind CSS v4** via `@tailwindcss/vite` — no `tailwind.config.js`; theme customization happens in `src/main.css` via `@theme`
- **`@tailwindcss/typography`** — loaded via `@plugin` in `main.css`; used for `.prose` blocks
- **Vite** for asset bundling (entry: `src/main.js`, output: `assets/`, gitignored)
- **Bun** as package manager and script runner

## Running locally

```bash
# Terminal 1 — PHP dev server
composer start

# Terminal 2 — CSS/JS watch
bun run dev
```

Site: `http://localhost:8000`
Panel: `http://localhost:8000/panel` (prompts to create the first admin account)

Always run `bun run build` (or keep `bun run dev` running) after changing CSS classes or JS — templates reference `assets/css/main.css` and `assets/js/main.js` directly, not the `src/` files.

## Project structure

```
content/            ← pages and uploaded files
site/
  blueprints/       ← Panel field definitions (pages/default.yml, site.yml)
  config/           ← config.php (email, SMTP, plugin settings) — not created yet
  plugins/          ← custom and third-party plugins — none yet
  snippets/         ← header.php, footer.php (shared page chrome)
  templates/        ← default.php — one .php per page type as the project grows
src/
  main.js           ← JS entry (imports main.css, mobile menu toggle)
  main.css          ← @import "tailwindcss" + @plugin "@tailwindcss/typography" + @theme
assets/             ← Vite build output (gitignored, rebuilt via bun run build)
```

## Key conventions

- Layout container: `max-w-5xl mx-auto px-4` — used in header, footer, and `<main>` to keep everything aligned. Adjust the max-width per project as needed.
- `header.php` and `footer.php` open/close the `<html>`/`<body>` tags (not split into separate `<head>` snippets) — every page template calls `snippet('header')` then `snippet('footer')`.
- Nav is driven by `$site->children()->listed()` — add pages in the Panel and they appear automatically; no manual nav config.
- No SEO plugin, no email config, no custom blueprints beyond the Plainkit defaults yet — add these per project as needed (see kirby-fotoalbum for a reference implementation using `tobimori/kirby-seo` and SMTP email). Docs: https://www.andkindness.com/seo — install via `composer require tobimori/kirby-seo`; handles meta tags, XML sitemaps, robots/indexing, Schema.org JSON-LD, Google Search Console, panel SEO previews/audits.
- Multi-language is opt-in via `./setup-languages.sh` (see README's "Multi-language support"), run once right after cloning. `site/snippets/language-switcher.php` is already wired into `header.php`'s desktop nav and mobile menu but renders nothing until multi-language mode is enabled, so the base stays single-language with no visual change until that script runs. When 2+ languages are chosen, the script also offers to install `johannschopplich/kirby-content-translator` (docs: https://kirby.tools/docs/content-translator/getting-started) — wires up DeepL or OpenAI/Copilot config in `site/config/config.php` via `env()` (never a literal key, since `config.php` is git-tracked) and adds its button to `site/blueprints/pages/default.yml`. Free locally, paid license at go-live — see README's "Licensing" section.

## Deploying

`deploy-example.sh` is the committed template — copy it to `deploy.sh` (gitignored, holds real server credentials) and fill in the target server's SSH/PHP/Composer details. `deploy.sh` does not exist in this repo yet since it's server-specific; each project cloned from this base creates its own. See the README's "Deploying to a live server" section for the full walkthrough.

`vendor/` and `kirby/` are never uploaded — Composer runs on the server after each deploy so dependencies build against the server's own PHP version.

## Starting a new project from this base

See the README section "Using this as a base for a new project" for the step-by-step (fresh git history, renaming `composer.json`, updating `site.yml` title, etc).
