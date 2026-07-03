<?php
require_once __DIR__ . '/../config/config.php';
requireAdmin();

$query = trim($_GET['q'] ?? '');
if ($query === '') {
    jsonResponse(['success' => false, 'message' => 'Enter a search term.'], 422);
}

$gb = new GoogleBooks();
$results = $gb->search($query, 12);

if (empty($results)) {
    jsonResponse(['success' => true, 'results' => [], 'message' => 'No results found, or the API could not be reached from this server.']);
}

jsonResponse(['success' => true, 'results' => $results]);