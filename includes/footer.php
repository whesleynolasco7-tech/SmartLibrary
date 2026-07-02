</main>
  </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<?php if (!empty($extraScripts)) foreach ($extraScripts as $script): ?>
<script src="<?= BASE_URL . $script ?>"></script>
<?php endforeach; ?>
</body>
</html>