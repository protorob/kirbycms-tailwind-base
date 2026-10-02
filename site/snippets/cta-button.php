<?php
// $class sets size (text + padding); language-switcher.php takes the same
// values so the two pills match. The transparent border mirrors the
// switcher's visible 1px border, keeping both the same height.
$ctaLabel = $site->ctaLabel();
$ctaUrl = $site->ctaUrl();
$ctaIcon = $site->ctaIcon();

if ($ctaLabel->isNotEmpty() && $ctaUrl->isNotEmpty()):
?>
  <a href="<?= esc($ctaUrl, 'attr') ?>" class="inline-flex items-center gap-2 border border-transparent bg-neutral-800 text-white font-medium rounded-full hover:bg-neutral-700 transition-colors [&>svg]:h-4 [&>svg]:w-4 [&>svg]:fill-current <?= $class ?? 'text-sm px-4 py-2' ?>">
    <?php if ($ctaIcon->isNotEmpty()): ?>
      <?= svg('/assets/icons/' . $ctaIcon) ?>
    <?php endif ?>
    <?= esc($ctaLabel) ?>
  </a>
<?php endif ?>
