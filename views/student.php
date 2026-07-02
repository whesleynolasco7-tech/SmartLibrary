<?php
require_once __DIR__ . '/../includes/auth_check.php';
requireAdmin();

$studentModel = new Student();
$students = $studentModel->getAll();

$pageTitle = 'Students';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
  <div>
    <h1>Students</h1>
    <p class="muted"><?= count($students) ?> registered students</p>
  </div>
</div>

<div class="card">
  <div class="card-body no-pad">
    <table class="data-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Student No.</th>
          <th>Course</th>
          <th>Year Level</th>
          <th>Contact</th>
          <th>Status</th>
          <th style="width:80px"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s): ?>
          <tr>
            <td>
              <div class="table-book">
                <img class="avatar-xs" src="<?= $s['profile_picture'] ? UPLOAD_AVATAR_URL . e($s['profile_picture']) : DEFAULT_AVATAR ?>" alt="">
                <div>
                  <div><?= e($s['name']) ?></div>
                  <div class="muted small"><?= e($s['email']) ?></div>
                </div>
              </div>
            </td>
            <td><?= e($s['student_number']) ?></td>
            <td><?= e($s['course'] ?: '—') ?></td>
            <td><?= e($s['year_level'] ?: '—') ?></td>
            <td><?= e($s['contact_number'] ?: '—') ?></td>
            <td><?= statusBadge($s['status']) ?></td>
            <td><a href="<?= BASE_URL ?>/views/student_profile.php?id=<?= $s['id'] ?>" class="icon-btn sm" title="View Profile"><i class="fa-regular fa-eye"></i></a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php if (empty($students)): ?>
      <div class="empty-state"><i class="fa-solid fa-user-graduate"></i><p>No students registered yet.</p></div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>