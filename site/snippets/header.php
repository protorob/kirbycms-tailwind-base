<?php
$navItems = $site->children()->listed();
$logo = $site->logo()->toFile();
?>
<!DOCTYPE html>
<html lang="<?= $kirby->language()?->code() ?? 'en' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($page->customTitle()->or($page->title())) ?> — <?= esc($site->title()) ?></title>
  <?= $page->meta()->robots() ?>
  <?= $page->meta()->social() ?>
  <?php if ($kirby->multilang()): ?>
    <?php foreach ($kirby->languages() as $language): ?>
      <link rel="alternate" hreflang="<?= JohannSchopplich\Helpers\Util::languageToHreflang($language) ?>" href="<?= $page->url($language->code()) ?>">
    <?php endforeach ?>
    <link rel="alternate" hreflang="x-default" href="<?= $page->url($kirby->defaultLanguage()->code()) ?>">
  <?php endif ?>
  <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
</head>
<body class="min-h-screen flex flex-col bg-white font-sans text-neutral-800 antialiased">

<?php /* relative z-40: keeps the desktop dropdowns above <main> (whose fade-in animation creates its own stacking context). */ ?>
<header id="site-header" class="relative z-40 border-b border-neutral-200">
  <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">

    <a href="<?= $site->url() ?>" class="flex items-center font-semibold tracking-tight text-lg">
      <?php if ($logo): ?>
        <img src="<?= $logo->url() ?>" alt="<?= esc($site->title()) ?>" class="h-8 w-auto">
      <?php else: ?>
        <?= $site->title() ?>
      <?php endif ?>
    </a>

    <div class="hidden sm:flex items-center gap-6">
      <nav class="flex items-center gap-6 text-sm">
        <?php foreach ($navItems as $item): ?>
          <?php $subItems = $item->children()->listed() ?>
          <?php if ($subItems->isNotEmpty()): ?>
            <?php /* CSS-only dropdown: shown on hover or keyboard focus (group-focus-within); `invisible` keeps hidden links out of the tab order until then. One level only. */ ?>
            <div class="relative group">
              <a href="<?= $item->url() ?>" class="inline-flex items-center gap-1 hover:opacity-60 transition-opacity <?= $item->isOpen() ? 'font-medium' : '' ?>">
                <?= $item->title() ?>
                <svg class="h-3 w-3 transition-transform group-hover:rotate-180 group-focus-within:rotate-180" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M2.5 4.5 6 8l3.5-3.5"/></svg>
              </a>
              <div class="absolute left-0 top-full z-50 pt-3 invisible opacity-0 transition-opacity group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                <ul class="min-w-48 rounded-lg border border-neutral-200 bg-white py-2 shadow-lg">
                  <?php foreach ($subItems as $sub): ?>
                    <li>
                      <a href="<?= $sub->url() ?>" class="block whitespace-nowrap px-4 py-2 hover:bg-neutral-50 <?= $sub->isOpen() ? 'font-medium' : '' ?>">
                        <?= $sub->title() ?>
                      </a>
                    </li>
                  <?php endforeach ?>
                </ul>
              </div>
            </div>
          <?php else: ?>
            <a href="<?= $item->url() ?>" class="hover:opacity-60 transition-opacity <?= $item->isOpen() ? 'font-medium' : '' ?>">
              <?= $item->title() ?>
            </a>
          <?php endif ?>
        <?php endforeach ?>
        <?php snippet('cta-button') ?>
      </nav>
      <?php snippet('language-switcher', ['id' => 'lang-desktop']) ?>
    </div>

    <div class="flex items-center gap-3 sm:hidden">
      <?php snippet('cta-button', ['class' => 'text-xs px-3 py-1.5']) ?>
      <button id="menu-toggle" class="p-2" aria-label="Toggle menu">
        <span class="block w-5 h-px bg-current mb-1.5"></span>
        <span class="block w-5 h-px bg-current mb-1.5"></span>
        <span class="block w-5 h-px bg-current"></span>
      </button>
    </div>
  </div>

  <nav id="mobile-menu" class="sm:hidden grid grid-rows-[0fr] opacity-0 -translate-y-1 pointer-events-none transition-all duration-200">
    <div class="overflow-hidden">
      <div class="border-t border-neutral-200 px-4 py-4 flex flex-col gap-4 text-sm">
        <?php foreach ($navItems as $item): ?>
          <div class="flex flex-col gap-3">
            <a href="<?= $item->url() ?>" class="<?= $item->isOpen() ? 'font-medium' : '' ?>">
              <?= $item->title() ?>
            </a>
            <?php /* Mobile has no hover, so children are always listed (indented) under their parent. */ ?>
            <?php foreach ($item->children()->listed() as $sub): ?>
              <a href="<?= $sub->url() ?>" class="pl-4 text-neutral-500 <?= $sub->isOpen() ? 'font-medium text-neutral-800' : '' ?>">
                <?= $sub->title() ?>
              </a>
            <?php endforeach ?>
          </div>
        <?php endforeach ?>
        <?php snippet('language-list') ?>
      </div>
    </div>
  </nav>
</header>
