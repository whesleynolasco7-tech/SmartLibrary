<?php
require_once __DIR__ . '/../includes/auth_check.php';

$borrowModel = new Borrow();
$borrowModel->markOverdue();

$db = Database::getInstance();

if (isAdmin()) {
    $records = $db->fetchAll(
        'SELECT r.*, b.title, b.author, b.cover_image, u.name AS student_name, s.student_number
         FROM borrowing_records r
         JOIN books b ON b.id = r.book_id
         JOIN students s ON s.id = r.student_id
         JOIN users u ON u.id = s.user_id
         ORDER BY r.borrow_date DESC LIMIT 100'
    );
    $students = $db->fetchAll('SELECT s.id, u.name, s.student_number FROM students s JOIN users u ON u.id = s.user_id ORDER BY u.name');
} else {
    $studentModel = new Student();
    $me = $studentModel->findByUserId($_SESSION['user_id']);
    $records = $me ? $studentModel->borrowingHistory($me['id']) : [];
    $students = [];
}

$bookModel = new Book();
$allBooks = $bookModel->getAll();
$csrf = generateCSRFToken();

$pageTitle = 'Borrowing Records';
$extraScripts = ['/assets/js/borrow.js'];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
  <div>
    <h1>Borrowing Records</h1>
    <p class="muted"><?= isAdmin() ? 'All borrow / return transactions' : 'Your borrowing history' ?></p>
  </div>
  <div class="page-head-actions">
    <button class="btn btn-primary" id="btnOpenBorrow"><i class="fa-solid fa-hand-holding"></i> Borrow a Book</button>
  </div>
</div>

<div class="card">
  <div class="card-body no-pad">
    <table class="data-table">
      <thead>
        <tr>
          <th>Book</th>
          <?php if (isAdmin()): ?><th>Student</th><?php endif; ?>
          <th>Borrow Date</th>
          <th>Due Date</th>
          <th>Return Date</th>
          <th>Status</th>
          <th>Fine</th>
          <th style="width:110px">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($records as $r): ?>
          <tr data-record-id="<?= $r['id'] ?>">
            <td>
              <div class="table-book">
                <img src="<?= $r['cover_image'] ? UPLOAD_COVER_URL . e($r['cover_image']) : DEFAULT_COVER ?>" alt="">
                <span><?= e($r['title']) ?></span>
              </div>
            </td>
            <?php if (isAdmin()): ?><td><?= e($r['student_name']) ?> <span class="muted small">(<?= e($r['student_number']) ?>)</span></td><?php endif; ?>
            <td><?= formatDate($r['borrow_date']) ?></td>
            <td><?= formatDate($r['due_date']) ?></td>
            <td><?= formatDate($r['return_date']) ?></td>
            <td><?= statusBadge($r['status']) ?></td>
            <td><?= $r['fine_amount'] > 0 ? '₱' . number_format($r['fine_amount'], 2) . ' (' . e($r['fine_status']) . ')' : '—' ?></td>
            <td>
              <?php if (in_array($r['status'], ['borrowed', 'overdue'], true)): ?>
                <button class="btn btn-sm btn-outline btn-return" data-id="<?= $r['id'] ?>"><i class="fa-solid fa-rotate-left"></i> Return</button>
              <?php else: ?>
                <span class="muted small">Completed</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($records)): ?>
      <div class="empty-state"><i class="fa-solid fa-right-left"></i><p>No borrowing records yet.</p></div>
    <?php endif; ?>
  </div>
</div>

<!-- Borrow Modal -->
<div class="modal-backdrop" id="borrowModalBackdrop">
  <div class="modal">
    <div class="modal-header">
      <h3><i class="fa-solid fa-hand-holding"></i> Borrow a Book</h3>
      <button class="icon-btn" id="closeBorrowModal"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-body">
      <?php if (isAdmin()): ?>
      <div class="form-group">
        <label>Student</label>
        <select id="borrowStudentSelect" class="select-input">
          <option value="">— Select Student —</option>
          <?php foreach ($students as $s): ?>
            <option value="<?= $s['id'] ?>"><?= e($s['name']) ?> (<?= e($s['student_number']) ?>)</option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <div class="form-group">
        <label>Book</label>
        <select id="borrowBookSelect" class="select-input">
          <option value="">— Select Book —</option>
          <?php foreach ($allBooks as $b): if ($b['available_copies'] < 1) continue; ?>
            <option value="<?= $b['id'] ?>"><?= e($b['title']) ?> — <?= (int)$b['available_copies'] ?> available</option>
          <?php endforeach; ?>
        </select>
      </div>
      <p class="muted small"><i class="fa-regular fa-circle-question"></i> Loan period is <?= LOAN_PERIOD_DAYS ?> days. A fine of ₱<?= FINE_PER_DAY ?>/day applies after the due date.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" id="cancelBorrow">Cancel</button>
      <button class="btn btn-primary" id="confirmBorrowBtn"><i class="fa-solid fa-check"></i> Confirm Borrow</button>
    </div>
  </div>
</div>

<input type="hidden" id="csrfToken" value="<?= e($csrf) ?>">
<input type="hidden" id="myStudentId" value="<?= $me['id'] ?? '' ?>">

<?php include __DIR__ . '/../includes/footer.php'; ?>