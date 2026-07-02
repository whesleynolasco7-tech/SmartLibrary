<?php
require_once __DIR__ . '/../includes/auth_check.php';

$id = (int) ($_GET['id'] ?? 0);
$bookModel = new Book();
$book = $bookModel->find($id);
if (!$book) {
    http_response_code(404);
    die('Book not found.');
}

$studentModel = new Student();
$myStudent = isAdmin() ? null : $studentModel->findByUserId($_SESSION['user_id']);
$csrf = generateCSRFToken();

$pageTitle = $book['title'];
$extraScripts = ['/assets/js/borrow.js'];
include __DIR__ . '/../includes/header.php';
?>

<a href="<?= BASE_URL ?>/views/books.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Catalog</a>

<div class="book-detail-layout">
  <div class="card book-detail-cover">
    <img src="<?= $book['cover_image'] ? UPLOAD_COVER_URL . e($book['cover_image']) : DEFAULT_COVER ?>" alt="<?= e($book['title']) ?>">
    <?php $available = $book['available_copies'] > 0; ?>
    <div class="availability-tag <?= $available ? 'ok' : 'no' ?>">
      <i class="fa-solid <?= $available ? 'fa-circle-check' : 'fa-circle-xmark' ?>"></i>
      <?= $available ? $book['available_copies'] . ' copies available' : 'Not available' ?>
    </div>
    <?php if ($available): ?>
      <button class="btn btn-primary btn-block" id="btnBorrowThis" data-book-id="<?= $book['id'] ?>"
        <?= (!$myStudent && !isAdmin()) ? 'disabled' : '' ?>>
        <i class="fa-solid fa-hand-holding"></i> Borrow This Book
      </button>
    <?php else: ?>
      <button class="btn btn-outline btn-block" disabled>Currently Unavailable</button>
    <?php endif; ?>
  </div>

  <div class="card book-detail-info">
    <span class="chip"><?= e($book['category_name'] ?? 'Uncategorized') ?></span>
    <h1><?= e($book['title']) ?></h1>
    <p class="muted">by <strong><?= e($book['author']) ?></strong></p>

    <div class="meta-grid">
      <div><span>ISBN</span><strong><?= e($book['isbn']) ?: '—' ?></strong></div>
      <div><span>Publisher</span><strong><?= e($book['publisher']) ?: '—' ?></strong></div>
      <div><span>Year</span><strong><?= e((string)$book['year_published']) ?: '—' ?></strong></div>
      <div><span>Language</span><strong><?= e($book['language']) ?></strong></div>
      <div><span>Total Copies</span><strong><?= (int)$book['total_copies'] ?></strong></div>
      <div><span>Available</span><strong><?= (int)$book['available_copies'] ?></strong></div>
    </div>

    <h3>Description</h3>
    <p><?= nl2br(e($book['description'] ?: 'No description available.')) ?></p>

    <?php if ($book['tags']): ?>
      <div class="tag-list">
        <?php foreach (explode(',', $book['tags']) as $tag): $tag = trim($tag); if ($tag === '') continue; ?>
          <span class="tag"><?= e($tag) ?></span>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3><i class="fa-solid fa-diagram-project"></i> Top 5 Similar Books <span class="muted small">(Content-Based Similarity Matching)</span></h3>
  </div>
  <div class="card-body">
    <div id="similarBooksContainer" class="similar-grid">
      <div class="loader-inline"><i class="fa-solid fa-spinner fa-spin"></i> Calculating similarity…</div>
    </div>
  </div>
</div>

<input type="hidden" id="csrfToken" value="<?= e($csrf) ?>">
<input type="hidden" id="myStudentId" value="<?= $myStudent['id'] ?? '' ?>">
<input type="hidden" id="bookDetailId" value="<?= $book['id'] ?>">

<?php include __DIR__ . '/../includes/footer.php'; ?>