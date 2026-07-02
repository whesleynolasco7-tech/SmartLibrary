<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$bookId = (int) ($_GET['book_id'] ?? 0);
if ($bookId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Missing book id.'], 422);
}

$recModel = new Recommendation();
$similar = $recModel->getSimilarBooks($bookId, 5);

$output = array_map(function ($b) {
    return [
        'id'         => $b['id'],
        'title'      => $b['title'],
        'author'     => $b['author'],
        'similarity' => $b['similarity'],
        'cover_url'  => $b['cover_image'] ? UPLOAD_COVER_URL . $b['cover_image'] : DEFAULT_COVER,
        'detail_url' => BASE_URL . '/views/book_details.php?id=' . $b['id'],
    ];
}, $similar);

jsonResponse(['success' => true, 'results' => $output]);