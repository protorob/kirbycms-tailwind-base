<?php
$ctaLabel = $site->ctaLabel();
$ctaUrl = $site->ctaUrl();
$ctaIcon = $site->ctaIcon();

if ($ctaLabel->isNotEmpty() && $ctaUrl->isNotEmpty()):
?>
  <a href="<?= $ctaUrl ?>" class="inline-flex items-center gap-2 bg-neutral-800 text-white font-medium rounded-full hover:bg-neutral-700 transition-colors [&>svg]:h-4 [&>svg]:w-4 [&>svg]:fill-current <?= $class ?? 'text-sm px-4 py-2' ?>">
    <?php if ($ctaIcon->isNotEmpty()): ?>
      <?= svg('/assets/icons/' . $ctaIcon) ?>
    <?php endif ?>
    <?= esc($ctaLabel) ?>
  </a>
<?php endif ?>
