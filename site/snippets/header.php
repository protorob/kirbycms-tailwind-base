<?php $navItems = $site->children()->listed() ?>
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
  <div class="max-w-5xl mx-auto px-4 h-16 flex items-center justify-between">

    <a href="<?= $site->url() ?>" class="font-semibold tracking-tight text-lg">
      <?= $site->title() ?>
    </a>

    <nav class="hidden sm:flex gap-6 text-sm">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= $item->url() ?>" class="hover:opacity-60 transition-opacity <?= $item->isActive() ? 'font-medium' : '' ?>">
          <?= $item->title() ?>
        </a>
      <?php endforeach ?>
    </nav>

    <button id="menu-toggle" class="sm:hidden p-2" aria-label="Toggle menu">
      <span class="block w-5 h-px bg-current mb-1.5"></span>
      <span class="block w-5 h-px bg-current mb-1.5"></span>
      <span class="block w-5 h-px bg-current"></span>
    </button>
  </div>

  <nav id="mobile-menu" class="sm:hidden border-t border-neutral-200 opacity-0 -translate-y-1 pointer-events-none transition-all duration-200">
    <div class="px-4 py-4 flex flex-col gap-4 text-sm">
      <?php foreach ($navItems as $item): ?>
        <a href="<?= $item->url() ?>" class="<?= $item->isActive() ? 'font-medium' : '' ?>">
          <?= $item->title() ?>
        </a>
      <?php endforeach ?>
    </div>
  </nav>
</header>
