<?php
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> · Smart Library</title>
<link rel="icon" href="<?= BASE_URL ?>/assets/images/covers/default-cover.svg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
  <?php include __DIR__ . '/sidebar.php'; ?>

  <div class="main-area">
    <header class="topbar">
      <button class="icon-btn sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu"><i class="fa-solid fa-bars"></i></button>

      <div class="topbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="globalSearch" placeholder="Search books by title, author, ISBN, category…" autocomplete="off">
        <div id="globalSearchResults" class="search-dropdown"></div>
      </div>

      <div class="topbar-actions">
        <button class="icon-btn" title="Notifications"><i class="fa-regular fa-bell"></i></button>
        <div class="user-chip" id="userMenuToggle">
          <img src="<?= DEFAULT_AVATAR ?>" alt="avatar" class="avatar-sm">
          <div class="user-chip-text">
            <strong><?= e($_SESSION['name'] ?? 'User') ?></strong>
            <span><?= e(ucfirst($_SESSION['role'] ?? '')) ?></span>
          </div>
          <i class="fa-solid fa-chevron-down"></i>
          <div class="user-menu" id="userMenu">
            <a href="<?= BASE_URL ?>/views/profile.php"><i class="fa-regular fa-user"></i> My Profile</a>
            <a href="<?= BASE_URL ?>/logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
          </div>
        </div>
      </div>
    </header>

    <main class="content">
      <div id="toastContainer" class="toast-container"></div>