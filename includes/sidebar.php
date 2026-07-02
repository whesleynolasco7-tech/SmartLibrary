<?php
$current = basename($_SERVER['PHP_SELF']);
function navActive(string $file, string $current): string
{
    return $file === $current ? 'active' : '';
}
?>
<aside class="sidebar" id="sidebar">
  <div class="brand">
    <div class="brand-icon"><i class="fa-solid fa-book-bookmark"></i></div>
    <div class="brand-text">
      <strong>Smart Library</strong>
      <span>Management System</span>
    </div>
  </div>

  <nav class="nav-menu">
    <span class="nav-section">Main</span>
    <a href="<?= BASE_URL ?>/views/dashboard.php" class="nav-link <?= navActive('dashboard.php', $current) ?>">
      <i class="fa-solid fa-gauge-high"></i> Dashboard
    </a>
    <a href="<?= BASE_URL ?>/views/books.php" class="nav-link <?= navActive('books.php', $current) ?>">
      <i class="fa-solid fa-book"></i> Book Catalog
    </a>
    <a href="<?= BASE_URL ?>/views/borrowing.php" class="nav-link <?= navActive('borrowing.php', $current) ?>">
      <i class="fa-solid fa-right-left"></i> Borrowing Records
    </a>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/views/student.php" class="nav-link <?= navActive('student.php', $current) ?>">
      <i class="fa-solid fa-user-graduate"></i> Students
    </a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/views/recommendations.php" class="nav-link <?= navActive('recommendations.php', $current) ?>">
      <i class="fa-solid fa-wand-magic-sparkles"></i> Recommendations
    </a>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/views/import_books.php" class="nav-link <?= navActive('import_books.php', $current) ?>">
      <i class="fa-brands fa-google"></i> Import from Google Books
    </a>
    <?php endif; ?>

    <span class="nav-section">Account</span>
    <a href="<?= BASE_URL ?>/views/profile.php" class="nav-link <?= navActive('profile.php', $current) ?>">
      <i class="fa-regular fa-user"></i> My Profile
    </a>
    <a href="<?= BASE_URL ?>/logout.php" class="nav-link logout-link">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </nav>

  <div class="sidebar-footer">
    <i class="fa-solid fa-circle-info"></i>
    <span>Smart Library v1.0<br>Academic OOP Project</span>
  </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>