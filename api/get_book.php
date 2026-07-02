<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$bookModel = new Book();
$book = $bookModel->find($id);

if (!$book) {
    jsonResponse(['success' => false, 'message' => 'Book not found.'], 404);
}
jsonResponse(['success' => true, 'book' => $book]);