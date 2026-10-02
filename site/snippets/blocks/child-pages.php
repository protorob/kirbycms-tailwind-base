<?php
/** @var \Kirby\Cms\Block $block */
$parent   = $block->page()->toPage();
$children = $parent?->children()->listed();

if (!$children || $children->isEmpty()) {
  return;
}

// Grid classes and image `sizes` per column count. Full class strings (not
// built from the number) so Tailwind's source scan finds them. 3 and 4
// columns only apply from lg: — at sm/md widths they'd be too cramped, so
// tablets get 2. `sizes` widths follow default.php's max-w-5xl container
// (64rem minus px-4 padding and the gap-6 gutters).
$layouts = [
  '2' => ['grid' => 'sm:grid-cols-2',               'sizes' => '(min-width: 64rem) 484px, (min-width: 40rem) 50vw, 100vw'],
  '3' => ['grid' => 'sm:grid-cols-2 lg:grid-cols-3', 'sizes' => '(min-width: 64rem) 315px, (min-width: 40rem) 50vw, 100vw'],
  '4' => ['grid' => 'sm:grid-cols-2 lg:grid-cols-4', 'sizes' => '(min-width: 64rem) 230px, (min-width: 40rem) 50vw, 100vw'],
];
$layout = $layouts[$block->columns()->value()] ?? $layouts['2'];
?>
<?php /* not-prose: the block sits inside default.php's .prose wrapper, which would restyle these links/headings */ ?>
<div class="not-prose my-10 grid gap-6 <?= $layout['grid'] ?>">
  <?php foreach ($children as $child): ?>
    <?php
    // Card image priority: the page's own cardImage, else its hero background
    // image, else none. The hero image only counts while the hero is actually
    // shown (toggle on + "Image" background type) — otherwise its fields are
    // hidden in the Panel, so a leftover image would be invisible to editors.
    $image = $child->cardImage()->toFile();
    if (!$image && $child->heroToggle()->toBool() && $child->heroBackgroundType()->or('image')->value() === 'image') {
      $image = $child->heroBackgroundImage()->toFile();
    }
    $excerpt = $child->pageExcerpt();
    ?>
    <?php /* One <a> around the whole card, so the image is clickable too (no nested links); `group` drives the image hover zoom. */ ?>
    <a href="<?= $child->url() ?>" class="group block overflow-hidden rounded-xl border border-neutral-200 hover:border-neutral-400 transition-colors">
      <?php if ($image): ?>
        <?php /* aspect-[3/2] = 6/4, matching the cardImage field's Panel preview ratio */ ?>
        <div class="aspect-[3/2] overflow-hidden">
          <?php // alt="" on purpose: the card's title is the link text, so the image is decorative ?>
          <img
            src="<?= $image->crop(900, 600)->url() ?>"
            srcset="<?= $image->crop(600, 400)->url() ?> 600w, <?= $image->crop(900, 600)->url() ?> 900w, <?= $image->crop(1400, 933)->url() ?> 1400w"
            sizes="<?= $layout['sizes'] ?>"
            alt=""
            loading="lazy"
            class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105"
          >
        </div>
      <?php endif ?>
      <div class="p-6">
        <h2 class="text-lg font-semibold"><?= $child->title()->esc() ?></h2>
        <?php if ($excerpt->isNotEmpty()): ?>
          <p class="mt-2 text-sm text-neutral-600"><?= $excerpt->esc() ?></p>
        <?php endif ?>
      </div>
    </a>
  <?php endforeach ?>
</div>
