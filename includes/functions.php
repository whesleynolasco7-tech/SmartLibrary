<?php
/**
 * Shared helper / utility functions
 */

function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        http_response_code(403);
        die('Access denied. Admin/Librarian only.');
    }
}

function generateCSRFToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && $token !== null && hash_equals($_SESSION['csrf_token'], $token);
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function formatDate(?string $date): string
{
    if (!$date) return '—';
    return date('M d, Y', strtotime($date));
}

function daysBetween(string $from, string $to): int
{
    $a = new DateTime($from);
    $b = new DateTime($to);
    return (int) $a->diff($b)->format('%r%a');
}

function calculateFine(string $dueDate, ?string $returnDate = null): float
{
    $end = $returnDate ?: date('Y-m-d');
    $overdueDays = daysBetween($dueDate, $end);
    return $overdueDays > 0 ? $overdueDays * FINE_PER_DAY : 0.0;
}

function sanitizeFilename(string $filename): string
{
    $filename = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);
    return substr($filename, 0, 150);
}

function uploadImage(array $file, string $targetDir, string $prefix = 'img'): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mime])) {
        return null;
    }
    $ext = $allowed[$mime];
    $newName = $prefix . '_' . uniqid() . '.' . $ext;
    $dest = rtrim($targetDir, '/') . '/' . $newName;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $newName;
    }
    return null;
}

function timeAgo(string $datetime): string
{
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' min ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hr ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' day(s) ago';
    return formatDate($datetime);
}

function flash(string $key, ?string $message = null)
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return;
    }
    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    return null;
}

function statusBadge(string $status): string
{
    $map = [
        'available' => 'badge-success',
        'borrowed'  => 'badge-warning',
        'returned'  => 'badge-success',
        'overdue'   => 'badge-danger',
        'active'    => 'badge-success',
        'inactive'  => 'badge-gray',
    ];
    $class = $map[strtolower($status)] ?? 'badge-gray';
    return '<span class="badge ' . $class . '">' . e(ucfirst($status)) . '</span>';
}