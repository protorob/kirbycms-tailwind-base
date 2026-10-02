<?php
// Mobile-menu language picker: a plain row of full language names at the
// bottom of #mobile-menu. Not the language-switcher.php dropdown — the menu's
// overflow-hidden (needed for its collapse animation) would clip it, and the
// menu is already open, so a second tap would be redundant. Renders nothing
// unless multi-language mode is on with 2+ languages.
if ($kirby->languages()->count() < 2) {
  return;
}

$current = $kirby->language()->code();
?>
<?php /* not-first: the divider only makes sense when nav items sit above it. */ ?>
<div class="flex items-center gap-4 border-neutral-200 not-first:border-t not-first:pt-4">
  <?php foreach ($kirby->languages() as $language): ?>
    <a
      href="<?= $page->url($language->code()) ?>"
      hreflang="<?= $language->code() ?>"
      lang="<?= $language->code() ?>"
      <?= $language->code() === $current ? 'aria-current="true"' : '' ?>
      class="<?= $language->code() === $current ? 'font-medium' : 'text-neutral-500' ?>"
    ><?= esc($language->name()) ?></a>
  <?php endforeach ?>
</div>
