#!/usr/bin/env bash
# Enable Kirby multi-language support for a new project cloned from this base.
# Run once, right after cloning, before customizing content further.
set -euo pipefail
cd "$(dirname "$0")"

PRESETS=(
  "en|English|en_US"
  "it|Italiano|it_IT"
  "es|Español|es_ES"
  "fr|Français|fr_FR"
  "de|Deutsch|de_DE"
  "nl|Nederlands|nl_NL"
  "pt|Português|pt_PT"
)

CONTENT_FILES=(content/site.txt content/home/home.txt content/error/error.txt)

echo "Enable multi-language support for this project? [y/N]"
read -r enable
if [[ ! "$enable" =~ ^[Yy]$ ]]; then
  echo "Skipping. Site stays single-language."
  exit 0
fi

if [[ ! -f content/home/home.txt ]]; then
  echo "content/home/home.txt not found — content already looks multi-language, or this isn't a fresh clone. Aborting."
  exit 1
fi

echo
echo "Available languages:"
for i in "${!PRESETS[@]}"; do
  IFS='|' read -r code name locale <<< "${PRESETS[$i]}"
  printf "  %d) %-12s %s (%s)\n" "$((i+1))" "$name" "$code" "$locale"
done
echo
echo "Enter the numbers of the languages to install, space-separated (e.g. 1 2)."
echo "You can also add languages not listed as code:Name:locale (e.g. nl:Nederlands:nl_NL)."
read -r -p "> " selection

declare -a chosen=()
for token in $selection; do
  if [[ "$token" =~ ^[0-9]+$ ]]; then
    idx=$((token - 1))
    if [[ $idx -ge 0 && $idx -lt ${#PRESETS[@]} ]]; then
      chosen+=("${PRESETS[$idx]}")
    else
      echo "Ignoring invalid selection: $token"
    fi
  elif [[ "$token" == *:*:* ]]; then
    IFS=':' read -r code name locale <<< "$token"
    chosen+=("$code|$name|$locale")
  else
    echo "Ignoring unrecognized entry: $token"
  fi
done

if [[ ${#chosen[@]} -eq 0 ]]; then
  echo "No valid languages selected. Aborting."
  exit 1
fi

echo
echo "Selected:"
for c in "${chosen[@]}"; do
  IFS='|' read -r code name locale <<< "$c"
  echo "  - $name ($code, $locale)"
done

echo
read -r -p "Which language code is the default? " default_code
found=0
for c in "${chosen[@]}"; do
  IFS='|' read -r code _ _ <<< "$c"
  [[ "$code" == "$default_code" ]] && found=1
done
if [[ $found -eq 0 ]]; then
  echo "'$default_code' is not one of the selected languages. Aborting."
  exit 1
fi

# Optional: kirby.tools/content-translator for one-click Panel translation
translator_config=""
translator_env_hint=""
setup_translator="n"
if [[ ${#chosen[@]} -gt 1 ]]; then
  echo
  echo "Also set up kirby.tools/content-translator for one-click Panel translation?"
  echo "Free to test locally; requires a paid license before going live (see README's Licensing section). [y/N]"
  read -r setup_translator
fi

if [[ "$setup_translator" =~ ^[Yy]$ ]]; then
  if command -v composer >/dev/null 2>&1; then
    composer require johannschopplich/kirby-content-translator
  else
    echo "composer not found on PATH — once it's installed, run:"
    echo "  composer require johannschopplich/kirby-content-translator"
  fi

  echo
  echo "Translation provider:"
  echo "  1) DeepL"
  echo "  2) AI via Kirby Copilot (OpenAI)"
  echo "  3) Skip — configure the provider later"
  read -r -p "> " provider_choice

  case "$provider_choice" in
    1)
      translator_config="    'johannschopplich.content-translator' => [
        'DeepL' => [
            'apiKey' => env('DEEPL_API_KEY'),
        ],
    ],"
      translator_env_hint="DEEPL_API_KEY"
      ;;
    2)
      translator_config="    'johannschopplich.copilot' => [
        'provider' => 'openai',
        'providers' => [
            'openai' => [
                'apiKey' => env('OPENAI_API_KEY'),
            ],
        ],
    ],"
      translator_env_hint="OPENAI_API_KEY"
      ;;
    *)
      echo "Skipping provider config — add it to site/config/config.php later, see:"
      echo "  https://kirby.tools/docs/content-translator/getting-started/installation"
      ;;
  esac

  # wire the Panel button into the default page blueprint
  if [[ -f site/blueprints/pages/default.yml ]] && ! grep -q '^buttons:' site/blueprints/pages/default.yml; then
    printf 'buttons:\n  - open\n  - preview\n  - "-"\n  - settings\n  - content-translator\n  - languages\n  - status\n\n%s' \
      "$(cat site/blueprints/pages/default.yml)" > site/blueprints/pages/default.yml
    echo "Added the content-translator button to site/blueprints/pages/default.yml"
  else
    echo "site/blueprints/pages/default.yml already defines 'buttons:' — add 'content-translator' to that list yourself."
  fi
fi

# site/config/config.php
if [[ -f site/config/config.php ]]; then
  echo
  echo "site/config/config.php already exists — not overwriting it. Add this yourself:"
  echo "  'languages' => true,"
  if [[ -n "$translator_config" ]]; then
    echo "$translator_config"
  fi
else
  mkdir -p site/config
  {
    echo '<?php'
    echo
    echo 'return ['
    echo "    'languages' => true,"
    if [[ -n "$translator_config" ]]; then
      echo "$translator_config"
    fi
    echo '];'
  } > site/config/config.php
  echo "Created site/config/config.php"
fi

if [[ -n "$translator_env_hint" ]]; then
  echo "Set the $translator_env_hint environment variable before using translation (never commit the key — config.php is tracked in git)."
fi

# site/languages/{code}.php
mkdir -p site/languages
for c in "${chosen[@]}"; do
  IFS='|' read -r code name locale <<< "$c"
  is_default="false"
  url="/$code"
  if [[ "$code" == "$default_code" ]]; then
    is_default="true"
    url="/"
  fi
  cat > "site/languages/${code}.php" <<PHP
<?php

return [
    'code'      => '${code}',
    'default'   => ${is_default},
    'direction' => 'ltr',
    'locale'    => '${locale}',
    'name'      => '${name}',
    'url'       => '${url}',
];
PHP
  echo "Created site/languages/${code}.php"
done

# content files: add a language-suffixed copy per selected language
for f in "${CONTENT_FILES[@]}"; do
  dir=$(dirname "$f")
  base=$(basename "$f" .txt)
  for c in "${chosen[@]}"; do
    IFS='|' read -r code _ _ <<< "$c"
    cp "$f" "$dir/${base}.${code}.txt"
  done
  rm "$f"
done
echo "Migrated content files to per-language copies (translate the non-default ones via the Panel)."

echo
echo "Done. Default language: $default_code. Run 'composer install && bun install' if you haven't, then 'composer start'."
if [[ "$setup_translator" =~ ^[Yy]$ ]]; then
  echo "content-translator is free to use locally. Activate a license in the Panel's System view before going live."
fi
