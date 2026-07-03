<?php
$pageTitle = $pageTitle ?? 'Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title><?= e($pageTitle) ?> · Smart Library Management System</title>

<!-- Browser Icon -->
<link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.png">

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

    <!-- Mobile Menu -->
    <button class="icon-btn sidebar-toggle" id="sidebarToggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Search -->
    <div class="topbar-search">
        <i class="fa-solid fa-magnifying-glass"></i>

        <input
            type="text"
            id="globalSearch"
            placeholder="Search books..."
            autocomplete="off">

        <div id="globalSearchResults" class="search-dropdown"></div>
    </div>

    <!-- Right Side -->
    <div class="topbar-actions">

        <!-- Notification -->
        <button class="icon-btn" title="Notifications">
            <i class="fa-regular fa-bell"></i>
        </button>

        <?php
        $avatar = DEFAULT_AVATAR;

        if (!empty($_SESSION['profile_picture'])) {
            $avatar = UPLOAD_AVATAR_URL . e($_SESSION['profile_picture']);
        }
        ?>

        <div class="user-chip" id="userMenuToggle">

            <img
                src="<?= $avatar ?>"
                class="avatar-sm"
                alt="Profile">

            <div class="user-chip-text">
                <strong><?= e($_SESSION['name'] ?? 'User') ?></strong>
                <span><?= e(ucfirst($_SESSION['role'] ?? '')) ?></span>
            </div>

            <i class="fa-solid fa-chevron-down"></i>

            <div class="user-menu" id="userMenu">

                <a href="<?= BASE_URL ?>/views/profile.php">
                    <i class="fa-regular fa-user"></i>
                    Profile
                </a>

                <a href="<?= BASE_URL ?>/logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    Logout
                </a>

            </div>

        </div>

    </div>

</header>

<main class="content">

<div id="toastContainer" class="toast-container"></div>