<?php
/**
 * Smart Library Management System
 * Global Configuration
 */

// ------------------------------
// Error Reporting
// ------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ------------------------------
// Database Configuration
// ------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_library');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ------------------------------
// Project Paths
// ------------------------------
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', '/SmartLibrary'); // Change this if your folder name is different

// ------------------------------
// Upload Paths
// ------------------------------
define('UPLOAD_COVER_DIR', BASE_PATH . '/uploads/covers/');
define('UPLOAD_AVATAR_DIR', BASE_PATH . '/uploads/avatars/');

define('UPLOAD_COVER_URL', BASE_URL . '/uploads/covers/');
define('UPLOAD_AVATAR_URL', BASE_URL . '/uploads/avatars/');

define('DEFAULT_COVER', BASE_URL . '/assets/images/covers/default-cover.svg');
define('DEFAULT_AVATAR', BASE_URL . '/assets/images/avatars/default-avatar.svg');

// ------------------------------
// Google Books API
// ------------------------------
define('GOOGLE_BOOKS_API_KEY', ''); // Optional
define('GOOGLE_BOOKS_API_URL', 'https://www.googleapis.com/books/v1/volumes');

// ------------------------------
// Library Settings
// ------------------------------
define('LOAN_PERIOD_DAYS', 7);
define('FINE_PER_DAY', 5);

// ------------------------------
// Timezone
// ------------------------------
date_default_timezone_set('Asia/Manila');

// ------------------------------
// Session
// ------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------
// Autoload Classes
// ------------------------------
spl_autoload_register(function ($class) {
    $file = BASE_PATH . '/classes/' . $class . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// ------------------------------
// Required Files
// ------------------------------
require_once BASE_PATH . '/config/Database.php';
require_once BASE_PATH . '/includes/functions.php';