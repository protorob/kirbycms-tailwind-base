<?php
// $model is passed explicitly by the caller: $site for the homepage's
// site-wide hero, or $page for a per-page hero (see default.php).
$model ??= $site;

$eyebrow         = $model->eyebrow();
$heroTitle       = $model->heroTitle();
$heroDescription = $model->heroDescription();
$heroButtons     = $model->heroButtons()->toStructure();

if (
  $eyebrow->isEmpty() &&
  $heroTitle->isEmpty() &&
  $heroDescription->isEmpty() &&
  $heroButtons->isEmpty()
) {
  return;
}

$fullWidth       = $model->heroFullWidth()->toBool();
$backgroundType  = $model->heroBackgroundType()->or('image')->value();
$backgroundImage = $model->heroBackgroundImage()->toFile();
$hasOverlay      = $backgroundType === 'image' && $model->heroImageOverlay()->toBool();

// Dynamic values are escaped once for the 'attr' context, since they all
// land inside an HTML style="..." attribute — the browser HTML-decodes
// the attribute before handing it to the CSS parser.
$heroStyle = '';
if ($backgroundType === 'color' && $model->heroBackgroundColor()->isNotEmpty()) {
  $heroStyle = 'background-color: ' . esc($model->heroBackgroundColor(), 'attr') . ';';
} elseif ($backgroundType === 'image' && $backgroundImage) {
  $heroStyle = "background-image: url('" . esc($backgroundImage->url(), 'attr') . "'); background-size: cover; background-position: center;";
}

$textStyle = $model->heroTextColor()->isNotEmpty()
  ? 'color: ' . esc($model->heroTextColor(), 'attr') . ';'
  : '';
?>
<section class="<?= $fullWidth ? '' : 'max-w-6xl mx-auto px-4 pt-3' ?>">
  <div class="relative overflow-hidden <?= $fullWidth ? '' : 'rounded-xl' ?>" style="<?= $heroStyle ?>">
    <?php if ($hasOverlay && $model->heroOverlayColor()->isNotEmpty()): ?>
      <div class="absolute inset-0" style="background-color: <?= esc($model->heroOverlayColor(), 'attr') ?>;"></div>
    <?php endif ?>

    <div class="relative max-w-5xl mx-auto px-4 sm:px-12 py-10 sm:py-20 text-center" style="<?= $textStyle ?>">
      <?php if ($eyebrow->isNotEmpty()): ?>
        <p class="text-sm font-medium uppercase tracking-wide opacity-70"><?= $eyebrow->esc() ?></p>
      <?php endif ?>

      <?php if ($heroTitle->isNotEmpty()): ?>
        <h1 class="mt-3 text-4xl sm:text-5xl font-semibold tracking-tight"><?= $heroTitle->esc() ?></h1>
      <?php endif ?>

      <?php if ($heroDescription->isNotEmpty()): ?>
        <p class="mt-4 text-lg opacity-80 max-w-2xl mx-auto [&_a]:underline"><?= $heroDescription->kti() ?></p>
      <?php endif ?>

      <?php if ($heroButtons->isNotEmpty()): ?>
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
          <?php foreach ($heroButtons as $button): ?>
            <?php
            $btnColor     = $button->color()->or('#171717');
            $btnTextColor = $button->textColor()->or('#ffffff');
            ?>
            <a href="<?= esc($button->url(), 'attr') ?>"
               style="background-color: <?= esc($btnColor, 'attr') ?>; color: <?= esc($btnTextColor, 'attr') ?>;"
               class="inline-flex items-center gap-2 font-medium rounded-full px-5 py-2.5 hover:opacity-90 transition-opacity [&>svg]:h-4 [&>svg]:w-4 [&>svg]:fill-current">
              <?php if ($button->icon()->isNotEmpty()): ?>
                <?= svg('/assets/icons/' . $button->icon()) ?>
              <?php endif ?>
              <?= $button->label()->esc() ?>
            </a>
          <?php endforeach ?>
        </div>
      <?php endif ?>
    </div>
  </div>
</section>
