<?php

$host = "localhost";
$db   = "smart_library";
$user = "root";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );

    echo "<h2 style='color:green'>Database Connected Successfully!</h2>";
} catch (PDOException $e) {
    echo "<h2 style='color:red'>Connection Failed</h2>";
    echo $e->getMessage();
}