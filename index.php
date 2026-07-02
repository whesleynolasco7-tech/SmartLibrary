<?php
require_once __DIR__ . '/config/config.php';

if (isLoggedIn()) {
    redirect('views/dashboard.php');
} else {
    redirect('login.php');
}