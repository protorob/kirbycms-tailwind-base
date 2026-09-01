<?php
$navItems = $site->children()->listed();
$logo = $site->logo()->toFile();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page->title() ?> — <?= $site->title() ?></title>
  <link rel="stylesheet" href="<?= url('assets/css/main.css') ?>">
</head>
<body class="min-h-screen flex flex-col bg-white font-sans text-neutral-800 antialiased">

<header id="site-header" class="border-b border-neutral-200">
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
          <a href="<?= $item->url() ?>" class="hover:opacity-60 transition-opacity <?= $item->isActive() ? 'font-medium' : '' ?>">
            <?= $item->title() ?>
          </a>
        <?php endforeach ?>
        <?php snippet('cta-button') ?>
      </nav>
      <?php snippet('language-switcher') ?>
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
          <a href="<?= $item->url() ?>" class="<?= $item->isActive() ? 'font-medium' : '' ?>">
            <?= $item->title() ?>
          </a>
        <?php endforeach ?>
        <?php snippet('language-switcher') ?>
      </div>
    </div>
  </nav>
</header>
