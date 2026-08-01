# adaltavoce

A Kirby CMS site built on [Kirby Plainkit](https://github.com/getkirby/plainkit), managed with Composer.

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
