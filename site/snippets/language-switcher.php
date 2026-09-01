<?php if ($kirby->languages()->count() > 1): ?>
  <div class="flex items-center gap-3 text-sm">
    <?php foreach ($kirby->languages() as $language): ?>
      <a href="<?= $page->url($language->code()) ?>"
         class="uppercase <?= $kirby->language()->code() === $language->code() ? 'font-medium' : 'opacity-60 hover:opacity-100 transition-opacity' ?>">
        <?= $language->code() ?>
      </a>
    <?php endforeach ?>
  </div>
<?php endif ?>
