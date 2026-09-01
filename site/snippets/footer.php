<?php
$social = $site->social()->toStructure();
$privacyPage = $site->privacyPage()->toPage();
$cookiePage = $site->cookiePage()->toPage();
?>

<footer class="border-t border-neutral-200 mt-auto">
  <div class="max-w-6xl mx-auto px-4 py-12 flex flex-col gap-8">

    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-8">
      <div class="text-sm text-neutral-500 space-y-1">
        <?php if ($site->companyName()->isNotEmpty()): ?>
          <p class="font-medium text-neutral-700"><?= $site->companyName() ?></p>
        <?php endif ?>
        <?php if ($site->companyAddress()->isNotEmpty()): ?>
          <p><?= nl2br(esc($site->companyAddress())) ?></p>
        <?php endif ?>
        <?php if ($site->companyPhone()->isNotEmpty()): ?>
          <p><a href="tel:<?= $site->companyPhone() ?>" class="hover:text-neutral-800 transition-colors"><?= $site->companyPhone() ?></a></p>
        <?php endif ?>
        <?php if ($site->email()->isNotEmpty()): ?>
          <p><a href="mailto:<?= $site->email() ?>" class="hover:text-neutral-800 transition-colors"><?= $site->email() ?></a></p>
        <?php endif ?>
      </div>

      <?php if ($social->isNotEmpty()): ?>
        <div class="flex items-center gap-4">
          <?php foreach ($social as $link): ?>
            <a href="<?= $link->url() ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= esc($link->label()) ?>" class="text-neutral-400 hover:text-neutral-800 transition-colors [&>svg]:h-5 [&>svg]:w-5 [&>svg]:fill-current">
              <?php if ($link->icon()->isNotEmpty()): ?>
                <?= svg('/assets/icons/' . $link->icon()) ?>
              <?php endif ?>
            </a>
          <?php endforeach ?>
        </div>
      <?php endif ?>
    </div>

    <div class="pt-8 border-t border-neutral-100 text-sm text-neutral-400 flex flex-col sm:flex-row items-center justify-between gap-2">
      <span>&copy; <?= date('Y') ?> <?= $site->title() ?></span>

      <?php if ($privacyPage || $cookiePage): ?>
        <nav class="flex gap-4">
          <?php if ($privacyPage): ?>
            <a href="<?= $privacyPage->url() ?>" class="hover:text-neutral-800 transition-colors"><?= $privacyPage->title() ?></a>
          <?php endif ?>
          <?php if ($cookiePage): ?>
            <a href="<?= $cookiePage->url() ?>" class="hover:text-neutral-800 transition-colors"><?= $cookiePage->title() ?></a>
          <?php endif ?>
        </nav>
      <?php endif ?>
    </div>

  </div>
</footer>

<script type="module" src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
