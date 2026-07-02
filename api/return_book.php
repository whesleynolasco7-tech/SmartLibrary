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

$recordId = (int) ($input['record_id'] ?? 0);
if ($recordId <= 0) {
    jsonResponse(['success' => false, 'message' => 'Missing record id.'], 422);
}

$borrowModel = new Borrow();
$result = $borrowModel->returnBook($recordId);
jsonResponse($result, $result['success'] ? 200 : 400);