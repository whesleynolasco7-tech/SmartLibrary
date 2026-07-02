<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

$term = trim($_GET['q'] ?? '');
$bookModel = new Book();
$results = $term !== '' ? $bookModel->search($term, 20) : $bookModel->getAll(20, 0);

$output = array_map(function ($b) {
    return [
        'id'                => $b['id'],
        'title'             => $b['title'],
        'author'            => $b['author'],
        'isbn'              => $b['isbn'],
        'category_name'     => $b['category_name'],
        'available_copies'  => (int) $b['available_copies'],
        'total_copies'      => (int) $b['total_copies'],
        'cover_url'         => $b['cover_image'] ? UPLOAD_COVER_URL . $b['cover_image'] : DEFAULT_COVER,
        'detail_url'        => BASE_URL . '/views/book_details.php?id=' . $b['id'],
    ];
}, $results);

jsonResponse(['success' => true, 'results' => $output, 'count' => count($output)]);