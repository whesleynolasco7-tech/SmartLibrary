<?php
require_once __DIR__ . '/../includes/auth_check.php';

$bookModel = new Book();
$studentModel = new Student();
$borrowModel = new Borrow();
$recModel = new Recommendation();

$borrowModel->markOverdue();

$stats = [
    'total_books'      => $bookModel->count(),
    'available_books'  => $bookModel->countAvailable(),
    'borrowed_books'   => $borrowModel->countBorrowed(),
    'returned_books'   => $borrowModel->countReturned(),
    'active_students'  => $studentModel->countActive(),
    'overdue'          => $borrowModel->countOverdue(),
];

$recentActivity = $borrowModel->getRecent(6);
$recommended = $recModel->getPopularBooks(4);

$pageTitle = 'Dashboard';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
  <div>
    <h1>Welcome back, <?= e(explode(' ', $_SESSION['name'])[0]) ?> 👋</h1>
    <p class="muted">Here's what's happening in your library today.</p>
  </div>
  <?php if (isAdmin()): ?>
  <div class="page-head-actions">
    <button class="btn btn-primary" onclick="location.href='<?= BASE_URL ?>/views/books.php?action=add'">
      <i class="fa-solid fa-plus"></i> Add Book
    </button>
  </div>
  <?php endif; ?>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-icon blue"><i class="fa-solid fa-book"></i></div>
    <div><span class="stat-value"><?= $stats['total_books'] ?></span><span class="stat-label">Total Books</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i class="fa-solid fa-book-open"></i></div>
    <div><span class="stat-value"><?= $stats['available_books'] ?></span><span class="stat-label">Available Books</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange"><i class="fa-solid fa-right-left"></i></div>
    <div><span class="stat-value"><?= $stats['borrowed_books'] ?></span><span class="stat-label">Borrowed Books</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon teal"><i class="fa-solid fa-rotate-left"></i></div>
    <div><span class="stat-value"><?= $stats['returned_books'] ?></span><span class="stat-label">Returned Books</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple"><i class="fa-solid fa-user-graduate"></i></div>
    <div><span class="stat-value"><?= $stats['active_students'] ?></span><span class="stat-label">Active Students</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon red"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div><span class="stat-value"><?= $stats['overdue'] ?></span><span class="stat-label">Overdue Loans</span></div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <h3><i class="fa-solid fa-clock-rotate-left"></i> Recent Borrowing Activity</h3>
      <a href="<?= BASE_URL ?>/views/borrowing.php" class="link">View all</a>
    </div>
    <div class="card-body no-pad">
      <?php if (empty($recentActivity)): ?>
        <div class="empty-state small"><i class="fa-regular fa-folder-open"></i><p>No borrowing activity yet.</p></div>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>Book</th><th>Student</th><th>Borrowed</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($recentActivity as $rec): ?>
            <tr>
              <td>
                <div class="table-book">
                  <img src="<?= $rec['cover_image'] ? UPLOAD_COVER_URL . e($rec['cover_image']) : DEFAULT_COVER ?>" alt="">
                  <span><?= e($rec['title']) ?></span>
                </div>
              </td>
              <td><?= e($rec['student_name']) ?></td>
              <td><?= formatDate($rec['borrow_date']) ?></td>
              <td><?= statusBadge($rec['status']) ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
    </div>

    <div class="card-body">
        <div class="quick-actions">

            <?php if (isAdmin()): ?>

                <!-- ADMIN ONLY -->

                <a href="<?= BASE_URL ?>/views/books.php?action=add" class="quick-action-btn">
                    <i class="fa-solid fa-book-medical"></i>
                    Add New Book
                </a>

                <a href="<?= BASE_URL ?>/views/books.php" class="quick-action-btn">
                    <i class="fa-solid fa-book"></i>
                    Manage Books
                </a>

                <a href="<?= BASE_URL ?>/views/student.php" class="quick-action-btn">
                    <i class="fa-solid fa-user-graduate"></i>
                    Manage Students
                </a>

                <a href="<?= BASE_URL ?>/views/borrowing.php" class="quick-action-btn">
                    <i class="fa-solid fa-right-left"></i>
                    Borrowing Records
                </a>

                <a href="<?= BASE_URL ?>/views/import_books.php" class="quick-action-btn">
                    <i class="fa-brands fa-google"></i>
                    Import Google Books
                </a>

                <a href="<?= BASE_URL ?>/views/recommendations.php" class="quick-action-btn">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                    Recommendations
                </a>

            <?php else: ?>

                <!-- STUDENT ONLY -->

                <a href="<?= BASE_URL ?>/views/books.php" class="quick-action-btn">
                    <i class="fa-solid fa-book"></i>
                    Browse Books
                </a>

                <a href="<?= BASE_URL ?>/views/borrowing.php" class="quick-action-btn">
                    <i class="fa-solid fa-book-open-reader"></i>
                    My Borrowed Books
                </a>

                <a href="<?= BASE_URL ?>/views/recommendations.php" class="quick-action-btn">
                    <i class="fa-solid fa-star"></i>
                    Recommended Books
                </a>

                <a href="<?= BASE_URL ?>/views/profile.php" class="quick-action-btn">
                    <i class="fa-solid fa-user"></i>
                    My Profile
                </a>

            <?php endif; ?>

        </div>
    </div>
</div>

<div class="card">
  <div class="card-header">
    <h3><i class="fa-solid fa-star"></i> Recommended Books</h3>
    <a href="<?= BASE_URL ?>/views/recommendations.php" class="link">See more</a>
  </div>
  <div class="card-body">
    <div class="book-grid">
      <?php foreach ($recommended as $book): ?>
        <a href="<?= BASE_URL ?>/views/book_details.php?id=<?= $book['id'] ?>" class="book-card">
          <img src="<?= $book['cover_image'] ? UPLOAD_COVER_URL . e($book['cover_image']) : DEFAULT_COVER ?>" alt="<?= e($book['title']) ?>">
          <div class="book-card-body">
            <strong><?= e($book['title']) ?></strong>
            <span><?= e($book['author']) ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>