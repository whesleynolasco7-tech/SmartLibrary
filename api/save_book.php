<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$title = trim($_POST['title'] ?? '');
$author = trim($_POST['author'] ?? '');

if ($title === '' || $author === '') {
    jsonResponse(['success' => false, 'message' => 'Title and Author are required.'], 422);
}

$bookId = (int)($_POST['book_id'] ?? 0);

$data = [
    'isbn'           => trim($_POST['isbn'] ?? ''),
    'title'          => $title,
    'author'         => $author,
    'publisher'      => trim($_POST['publisher'] ?? ''),
    'category_id'    => (int)($_POST['category_id'] ?? 0),
    'description'    => trim($_POST['description'] ?? ''),
    'year_published' => (int)($_POST['year_published'] ?? 0),
    'language'       => trim($_POST['language'] ?? 'English'),
    'total_copies'   => max(1, (int)($_POST['total_copies'] ?? 1)),
    'tags'           => trim($_POST['tags'] ?? '')
];

$bookModel = new Book();

if ($bookId > 0) {

    $existing = $bookModel->find($bookId);

    if (!$existing) {
        jsonResponse([
            'success' => false,
            'message' => 'Book not found.'
        ], 404);
    }

    // Keep the old cover by default
    $data['cover_image'] = $existing['cover_image'];

    // Upload a new cover only if the user selected one
    if (
        isset($_FILES['cover_image']) &&
        $_FILES['cover_image']['error'] === UPLOAD_ERR_OK &&
        !empty($_FILES['cover_image']['name'])
    ) {
        $filename = uploadImage($_FILES['cover_image'], UPLOAD_COVER_DIR, 'book');

        if ($filename) {
            $data['cover_image'] = $filename;
        }
    }

    $borrowedCount = $existing['total_copies'] - $existing['available_copies'];
    $newAvailable = max(0, $data['total_copies'] - $borrowedCount);

    $bookModel->update($bookId, $data);

    $db = Database::getInstance();
    $db->execute(
        "UPDATE books SET available_copies = :available WHERE id = :id",
        [
            'available' => $newAvailable,
            'id' => $bookId
        ]
    );

    jsonResponse([
        'success' => true,
        'message' => 'Book updated successfully.',
        'id' => $bookId
    ]);

} else {

    // New book

    if (
        isset($_FILES['cover_image']) &&
        $_FILES['cover_image']['error'] === UPLOAD_ERR_OK &&
        !empty($_FILES['cover_image']['name'])
    ) {
        $filename = uploadImage($_FILES['cover_image'], UPLOAD_COVER_DIR, 'book');

        if ($filename) {
            $data['cover_image'] = $filename;
        }
    }

    $data['available_copies'] = $data['total_copies'];

    $newId = $bookModel->create($data);

    jsonResponse([
        'success' => true,
        'message' => 'Book added successfully.',
        'id' => $newId
    ]);
}