</main>
<footer class="text-center text-muted small py-4 border-top">
  &copy; <?= date('Y') ?> <?= APP_NAME ?> &middot; Web Development Final Project
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>"></script>
<?php if (!empty($extraScripts)) echo $extraScripts; ?>
</body>
</html>
