<?php snippet('header') ?>

<main class="flex-1 w-full">

  <div class="max-w-5xl mx-auto px-4 py-16">
    <h1 class="text-3xl font-semibold tracking-tight"><?= $page->title() ?></h1>

    <?php if ($page->text()->isNotEmpty()): ?>
      <div class="mt-8 prose max-w-none">
        <?= $page->text()->kt() ?>
      </div>
    <?php endif ?>
  </div>

</main>

<?php snippet('footer') ?>
