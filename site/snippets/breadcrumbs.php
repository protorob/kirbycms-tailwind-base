<?php
// Breadcrumb trail (Home › Parent › Current), only on pages nested under
// another page. Accessible pattern: a labelled <nav> landmark with an ordered
// list, the current page marked aria-current="page" (not a link), and the
// separators hidden from screen readers. Also emits schema.org BreadcrumbList
// JSON-LD so search engines can show the path in results.
if (!$page->parent()) {
  return;
}

$crumbs = [$site->homePage(), ...$page->parents()->flip()->values(), $page];
$last   = count($crumbs) - 1;

$jsonld = [
  '@context'        => 'https://schema.org',
  '@type'           => 'BreadcrumbList',
  'itemListElement' => array_map(fn ($crumb, $i) => [
    '@type'    => 'ListItem',
    'position' => $i + 1,
    'name'     => $crumb->title()->value(),
    'item'     => $crumb->url(),
  ], $crumbs, array_keys($crumbs)),
];
?>
<nav aria-label="<?= esc(t('breadcrumbs.label', 'Breadcrumb'), 'attr') ?>" class="text-sm text-neutral-500">
  <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
    <?php foreach ($crumbs as $i => $crumb): ?>
      <li class="flex items-center gap-2">
        <?php if ($i > 0): ?>
          <svg class="h-3 w-3 shrink-0" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4.5 2.5 8 6l-3.5 3.5"/></svg>
        <?php endif ?>
        <?php if ($i === $last): ?>
          <span aria-current="page" class="font-medium text-neutral-800"><?= $crumb->title()->esc() ?></span>
        <?php else: ?>
          <a href="<?= $crumb->url() ?>" class="underline-offset-4 hover:text-neutral-800 hover:underline"><?= $crumb->title()->esc() ?></a>
        <?php endif ?>
      </li>
    <?php endforeach ?>
  </ol>
</nav>
<script type="application/ld+json"><?= json_encode($jsonld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
