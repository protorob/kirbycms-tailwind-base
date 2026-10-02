<?php
// Language dropdown, desktop nav only — the mobile menu uses
// language-list.php instead. Shows the current language code when closed,
// full language names when open. Renders nothing unless multi-language mode
// is on with 2+ languages. $id sets the menu's DOM id. Toggle/close logic
// lives in src/main.js. $class sets size (text + padding) and takes the same
// values as cta-button.php's, so the two pills sit side by side at the same
// size.
if ($kirby->languages()->count() < 2) {
  return;
}

$id ??= 'lang-switcher';
$current = $kirby->language()->code();
?>
<div class="relative" data-lang-switcher>
  <button
    type="button"
    data-lang-toggle
    aria-haspopup="true"
    aria-expanded="false"
    aria-controls="<?= $id ?>-menu"
    class="inline-flex items-center gap-1.5 rounded-full border border-current/30 font-medium uppercase tracking-wide hover:border-current/60 transition-colors <?= $class ?? 'text-sm px-4 py-2' ?>"
  >
    <?= $current ?>
    <svg width="10" height="6" viewBox="0 0 10 6" fill="none" data-lang-chevron class="transition-transform duration-200" aria-hidden="true">
      <path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  </button>

  <?php /* right-0: the switcher sits at the right edge of the header, so a left-anchored menu could overflow the viewport. */ ?>
  <div
    id="<?= $id ?>-menu"
    data-lang-menu
    class="hidden absolute right-0 z-50 mt-2 w-40 rounded-xl border border-neutral-200 bg-white py-2 text-neutral-800 shadow-lg"
  >
    <?php foreach ($kirby->languages() as $language): ?>
      <a
        href="<?= $page->url($language->code()) ?>"
        hreflang="<?= $language->code() ?>"
        lang="<?= $language->code() ?>"
        <?= $language->code() === $current ? 'aria-current="true"' : '' ?>
        class="block px-4 py-2 text-sm hover:bg-neutral-50 transition-colors <?= $language->code() === $current ? 'font-semibold' : '' ?>"
      ><?= esc($language->name()) ?></a>
    <?php endforeach ?>
  </div>
</div>
