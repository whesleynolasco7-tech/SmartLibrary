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

$id = (int) ($input['id'] ?? 0);
$bookModel = new Book();

$activeLoans = Database::getInstance()->fetchOne(
    "SELECT COUNT(*) AS c FROM borrowing_records WHERE book_id = :id AND status IN ('borrowed','overdue')",
    ['id' => $id]
);
if ($activeLoans && (int)$activeLoans['c'] > 0) {
    jsonResponse(['success' => false, 'message' => 'Cannot delete: this book has active loans.'], 409);
}

$ok = $bookModel->delete($id);
jsonResponse(['success' => $ok, 'message' => $ok ? 'Book deleted.' : 'Delete failed.']);