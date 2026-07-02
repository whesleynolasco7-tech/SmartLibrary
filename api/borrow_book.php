<?php
require_once __DIR__ . '/../config/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}
$input = json_decode(file_get_contents('php://input'), true) ?? [];
if (!verifyCSRFToken($input['csrf_token'] ?? null)) {
    jsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
}

$bookId = (int) ($input['book_id'] ?? 0);
$studentId = (int) ($input['student_id'] ?? 0);

// Students can only borrow for themselves
if (!isAdmin()) {
    $studentModel = new Student();
    $self = $studentModel->findByUserId($_SESSION['user_id']);
    if (!$self) {
        jsonResponse(['success' => false, 'message' => 'Student profile not found.'], 403);
    }
    $studentId = (int) $self['id'];
}

if ($bookId <= 0 || $studentId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Missing book or student.'], 422);
}

$borrowModel = new Borrow();
$result = $borrowModel->borrowBook($studentId, $bookId);
jsonResponse($result, $result['success'] ? 200 : 400);