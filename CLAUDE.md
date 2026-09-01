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
- Header/footer chrome (logo, header CTA, company info, social links, legal page links) is a built-in default, not something to rebuild per project — see README's "Site panel defaults". `site/blueprints/site.yml` has a Header tab (`ctaIcon`/`ctaLabel`/`ctaUrl`, rendered by `site/snippets/cta-button.php` and called from `header.php` as the last desktop nav item and always-visible on mobile) and a Company information tab (`logo` file field, company fields, a `social` structure using the `icon` field type, and a `legalPages` structure — one `page` picker per row, since a project may need more than the two legal pages this base ships with). `header.php`/`footer.php` render all of it with empty-state fallbacks; `content/privacy-policy/` and `content/cookie-policy/` are unlisted placeholder pages to fill in per client (add more legal pages the same way and list them as additional `legalPages` rows). Icons come from `tobimori/kirby-icon-field` (composer-installed into `site/plugins/kirby-icon-field/`, which is gitignored like `vendor/`/`kirby/` since it's composer-managed, not hand-written) reading SVGs from `assets/icons/` (tracked in git, unlike `assets/css`/`assets/js`) — add more platform SVGs there as needed. The plugin caches its folder scan by config, not by folder contents, so `site/config/config.php` sets `'tobimori.icon-field' => ['cache' => false]` for now — a new SVG dropped into `assets/icons/` would otherwise not appear in the Panel until `site/cache/<host>/tobimori/` is deleted. See README's "Site panel defaults".
- The homepage uses the plain `default.yml`/`default.php` like any other page — there's no dedicated home blueprint/template. The hero banner is a "Homepage hero section" field group living in `site/blueprints/site.yml`'s Header tab (on `$site`, not `$page`), rendered by `site/snippets/hero.php`, which `default.php` calls only when `$page->isHomePage()` — see README's "Homepage hero section". Hero/button colors are user-picked at runtime via Kirby's `color` field, so they're applied as inline `style` attributes, not Tailwind classes (Tailwind can't purge-safe-generate classes for values it can't see at build time) — each dynamic value is escaped once with `esc($value, 'attr')` since it lands inside an HTML attribute; escaping with `'css'` first and `'attr'` again double-encodes and corrupts the style string. Kirby's `when:` only supports "and" logic (single exact-value matches) without a plugin, so the background controls are split into orthogonal toggles/radios rather than one 3-way condition.
- `site/blueprints/pages/default.yml`'s `text` field is `type: blocks` (Kirby's visual block editor), not a textarea/KirbyText field — see README's "Default page content". `default.php` renders it via `$page->text()->toBlocks()->toHtml()` (not `->kt()`), still wrapped in `.prose` for `@tailwindcss/typography`. Content stores as a single-line JSON array in the `.txt` file (e.g. `Text: [{"type":"text","content":{"text":"<p>...</p>"},"id":"...","isHidden":false}]`), confirmed by round-tripping through `Kirby\Cms\Page::update()` rather than hand-writing it — the JSON shape isn't obvious from the blueprint alone.
- Multi-language is opt-in via `./setup-languages.sh` (see README's "Multi-language support"). `site/snippets/language-switcher.php` is already wired into `header.php`'s desktop nav and mobile menu but renders nothing until multi-language mode is enabled, so the base stays single-language with no visual change until that script runs. When 2+ languages are chosen, the script also offers to install `johannschopplich/kirby-content-translator` (docs: https://kirby.tools/docs/content-translator/getting-started) — wires up DeepL or OpenAI/Copilot config in `site/config/config.php` via `env()` (never a literal key, since `config.php` is git-tracked) and adds its button to `site/blueprints/pages/default.yml`. Free locally, paid license at go-live — see README's "Licensing" section.
  - Content migration is dynamic (`find content -name '*.txt'`), not a hardcoded file list — it covers however many pages exist at run time, since Kirby's content resolution (`PlainTextStorage::contentFilename()`) always requires `<template>.<langcode>.txt` once `'languages' => true`, with no fallback to the old bare filename — an un-migrated page silently reads as completely empty (`read()` returns `[]`), not an error.
  - Before migrating anything, it checks three independent signals for an already-multilingual site (`config.php`'s `'languages' => true`, existing files in `site/languages/`, or an existing language-suffixed content file like `home.en.txt`) and aborts with no changes if any are true — re-running it against an already-migrated site would otherwise risk orphaning content.

## Deploying

`deploy-example.sh` is the committed template — copy it to `deploy.sh` (gitignored, holds real server credentials) and fill in the target server's SSH/PHP/Composer details. `deploy.sh` does not exist in this repo yet since it's server-specific; each project cloned from this base creates its own. See the README's "Deploying to a live server" section for the full walkthrough.

`vendor/` and `kirby/` are never uploaded — Composer runs on the server after each deploy so dependencies build against the server's own PHP version.

## Starting a new project from this base

See the README section "Using this as a base for a new project" for the step-by-step (fresh git history, renaming `composer.json`, updating `site.yml` title, etc).
