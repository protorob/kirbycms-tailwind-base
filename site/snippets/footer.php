<footer class="border-t border-neutral-200 mt-auto">
  <div class="max-w-5xl mx-auto px-4 py-8 text-sm text-neutral-400 flex flex-col sm:flex-row items-center justify-between gap-2">
    <span>&copy; <?= date('Y') ?> <?= $site->title() ?></span>
    <?php if ($site->email()->isNotEmpty()): ?>
      <a href="mailto:<?= $site->email() ?>" class="hover:text-neutral-800 transition-colors"><?= $site->email() ?></a>
    <?php endif ?>
  </div>
</footer>

<script type="module" src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
