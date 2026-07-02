<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}
$input = json_decode(file_get_contents('php://input'), true) ?? [];
if (!verifyCSRFToken($input['csrf_token'] ?? null)) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$required = ['title', 'author'];
foreach ($required as $r) {
    if (empty($input[$r])) {
        jsonResponse(['success' => false, 'message' => "Missing field: $r"], 422);
    }
}

$db = Database::getInstance();
$bookModel = new Book();

// Resolve or create category
$categoryId = null;
if (!empty($input['categories'])) {
    $catName = trim(explode(',', $input['categories'])[0]);
    if ($catName !== '') {
        $existing = $db->fetchOne('SELECT id FROM categories WHERE name = :n', ['n' => $catName]);
        if ($existing) {
            $categoryId = $existing['id'];
        } else {
            $db->execute('INSERT INTO categories (name) VALUES (:n)', ['n' => $catName]);
            $categoryId = $db->lastInsertId();
        }
    }
}

// Download cover image locally if provided
$coverFilename = null;
if (!empty($input['thumbnail'])) {
    $gb = new GoogleBooks();
    $coverFilename = $gb->downloadCover($input['thumbnail']);
}

$year = null;
if (!empty($input['published_date'])) {
    $year = (int) substr($input['published_date'], 0, 4);
}

$bookId = $bookModel->create([
    'isbn'            => $input['isbn'] ?? '',
    'title'           => $input['title'],
    'author'          => $input['author'],
    'publisher'       => $input['publisher'] ?? '',
    'category_id'     => $categoryId,
    'description'     => $input['description'] ?? '',
    'year_published'  => $year,
    'language'        => $input['language'] ?? 'EN',
    'total_copies'    => 1,
    'available_copies'=> 1,
    'cover_image'     => $coverFilename,
    'tags'            => $input['categories'] ?? '',
]);

// Log the import for audit trail
$db->execute(
    'INSERT INTO imported_books (book_id, google_volume_id, imported_at) VALUES (:bid, :vid, NOW())',
    ['bid' => $bookId, 'vid' => $input['volume_id'] ?? null]
);

jsonResponse(['success' => true, 'message' => 'Book imported successfully.', 'id' => $bookId]);