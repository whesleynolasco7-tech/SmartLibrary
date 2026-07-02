<?php
require_once __DIR__ . '/../includes/auth_check.php';

$id = (int) ($_GET['id'] ?? 0);
$studentModel = new Student();
$student = $studentModel->find($id);
if (!$student) { http_response_code(404); die('Student not found.'); }

// Students may only view their own profile
if (!isAdmin() && $student['user_id'] != $_SESSION['user_id']) {
    http_response_code(403);
    die('Access denied.');
}

$history = $studentModel->borrowingHistory($id);
$favoriteCategories = $studentModel->favoriteCategories($id);
$recModel = new Recommendation();
$recommendations = $recModel->getPersonalizedRecommendations($id, 6);

$pageTitle = $student['name'];
include __DIR__ . '/../includes/header.php';
?>

<a href="<?= BASE_URL ?>/views/students.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Students</a>

<div class="profile-header card">
  <img src="<?= $student['profile_picture'] ? UPLOAD_AVATAR_URL . e($student['profile_picture']) : DEFAULT_AVATAR ?>" class="avatar-lg" alt="">
  <div>
    <h1><?= e($student['name']) ?></h1>
    <p class="muted"><?= e($student['course'] ?: 'No course set') ?> · <?= e($student['year_level'] ?: '—') ?></p>
    <div class="profile-meta">
      <span><i class="fa-regular fa-envelope"></i> <?= e($student['email']) ?></span>
      <span><i class="fa-solid fa-phone"></i> <?= e($student['contact_number'] ?: '—') ?></span>
      <span><i class="fa-solid fa-id-card"></i> <?= e($student['student_number']) ?></span>
    </div>
  </div>
  <?= statusBadge($student['status']) ?>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><h3><i class="fa-solid fa-clock-rotate-left"></i> Borrowing History</h3></div>
    <div class="card-body no-pad">
      <?php if (empty($history)): ?>
        <div class="empty-state small"><i class="fa-regular fa-folder-open"></i><p>No borrowing history yet.</p></div>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Book</th><th>Borrowed</th><th>Due</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($history as $h): ?>
            <tr>
              <td><div class="table-book"><img src="<?= $h['cover_image'] ? UPLOAD_COVER_URL . e($h['cover_image']) : DEFAULT_COVER ?>" alt=""><span><?= e($h['title']) ?></span></div></td>
              <td><?= formatDate($h['borrow_date']) ?></td>
              <td><?= formatDate($h['due_date']) ?></td>
              <td><?= statusBadge($h['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3><i class="fa-solid fa-heart"></i> Favorite Categories</h3></div>
    <div class="card-body">
      <?php if (empty($favoriteCategories)): ?>
        <p class="muted">Not enough borrowing history yet to determine preferences.</p>
      <?php else: ?>
        <div class="tag-list">
          <?php foreach ($favoriteCategories as $c): ?>
            <span class="tag"><?= e($c['name']) ?> <b>(<?= $c['total'] ?>)</b></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3><i class="fa-solid fa-wand-magic-sparkles"></i> Recommended For <?= e(explode(' ', $student['name'])[0]) ?></h3></div>
  <div class="card-body">
    <div class="book-grid">
      <?php foreach ($recommendations as $book): ?>
        <a href="<?= BASE_URL ?>/views/book_details.php?id=<?= $book['id'] ?>" class="book-card">
          <img src="<?= $book['cover_image'] ? UPLOAD_COVER_URL . e($book['cover_image']) : DEFAULT_COVER ?>" alt="">
          <div class="book-card-body">
            <strong><?= e($book['title']) ?></strong>
            <span><?= e($book['author']) ?></span>
            <?php if (isset($book['similarity'])): ?><span class="tag sm"><?= $book['similarity'] ?>% match</span><?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>