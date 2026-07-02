<?php
require_once __DIR__ . '/../includes/auth_check.php';

$userModel = new User();
$studentModel = new Student();

$student = isAdmin() ? null : $studentModel->findByUserId($_SESSION['user_id']);

$successMsg = null;
$errorMsg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCSRFToken($_POST['csrf_token'] ?? null)) {

    // ==========================
    // STUDENT PROFILE
    // ==========================
    if (isset($_POST['update_profile']) && $student) {

        $avatar = $student['profile_picture'];

        if (!empty($_FILES['profile_picture']['name'])) {

            $uploaded = uploadImage(
                $_FILES['profile_picture'],
                UPLOAD_AVATAR_DIR,
                'avatar'
            );

            if ($uploaded) {
                $avatar = $uploaded;
                $_SESSION['profile_picture'] = $avatar;
            }
        }

        $studentModel->update($student['id'], [
            'course' => trim($_POST['course'] ?? ''),
            'year_level' => trim($_POST['year_level'] ?? ''),
            'contact_number' => trim($_POST['contact_number'] ?? ''),
            'profile_picture' => $avatar
        ]);

        $student = $studentModel->findByUserId($_SESSION['user_id']);

        $successMsg = "Profile updated successfully.";
    }

    // ==========================
    // ADMIN PROFILE
    // ==========================
    elseif (isset($_POST['update_admin'])) {

        if (!empty($_FILES['profile_picture']['name'])) {

            $uploaded = uploadImage(
                $_FILES['profile_picture'],
                UPLOAD_AVATAR_DIR,
                'avatar'
            );

            if ($uploaded) {

                $userModel->updateProfilePicture(
                    $_SESSION['user_id'],
                    $uploaded
                );

                $_SESSION['profile_picture'] = $uploaded;

                $successMsg = "Profile picture updated successfully.";
            }
        }
    }

    // ==========================
    // CHANGE PASSWORD
    // ==========================
    elseif (isset($_POST['change_password'])) {

        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($new) < 6) {

            $errorMsg = "Password must be at least 6 characters.";

        } elseif ($new !== $confirm) {

            $errorMsg = "Passwords do not match.";

        } else {

            $userModel->changePassword($_SESSION['user_id'], $new);

            $successMsg = "Password changed successfully.";
        }
    }
}

$csrf = generateCSRFToken();

$pageTitle = "My Profile";

include __DIR__ . '/../includes/header.php';
?>

<div class="page-head">
    <div>
        <h1>My Profile</h1>
        <p class="muted">Manage your account details</p>
    </div>
</div>

<?php if ($successMsg): ?>
<div class="alert alert-success">
    <i class="fa-solid fa-circle-check"></i>
    <?= e($successMsg) ?>
</div>
<?php endif; ?>

<?php if ($errorMsg): ?>
<div class="alert alert-danger">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <?= e($errorMsg) ?>
</div>
<?php endif; ?>

<div class="grid-2">

<?php if ($student): ?>

<div class="card">

<div class="card-header">
<h3><i class="fa-regular fa-user"></i> Student Details</h3>
</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

<div style="text-align:center;margin-bottom:20px;">

<img
src="<?= $_SESSION['profile_picture'] ? UPLOAD_AVATAR_URL . e($_SESSION['profile_picture']) : DEFAULT_AVATAR ?>"
class="avatar-lg"
style="width:120px;height:120px;border-radius:50%;object-fit:cover;">

</div>

<div class="form-group">
<label>Full Name</label>
<input type="text" value="<?= e($student['name']) ?>" disabled>
</div>

<div class="form-group">
<label>Email</label>
<input type="text" value="<?= e($student['email']) ?>" disabled>
</div>

<div class="form-group">
<label>Student Number</label>
<input type="text" value="<?= e($student['student_number']) ?>" disabled>
</div>

<div class="form-group">
<label>Course</label>
<input type="text" name="course" value="<?= e($student['course']) ?>">
</div>

<div class="form-group">
<label>Year Level</label>
<input type="text" name="year_level" value="<?= e($student['year_level']) ?>">
</div>

<div class="form-group">
<label>Contact Number</label>
<input type="text" name="contact_number" value="<?= e($student['contact_number']) ?>">
</div>

<div class="form-group">
<label>Profile Picture</label>
<input type="file" name="profile_picture" accept="image/*">
</div>

<button class="btn btn-primary" name="update_profile">
<i class="fa-solid fa-floppy-disk"></i>
Save Changes
</button>

</form>

</div>

</div>

<?php else: ?>

<div class="card">

<div class="card-header">
<h3><i class="fa-solid fa-user-tie"></i> Admin / Librarian Account</h3>
</div>

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

<div style="text-align:center;margin-bottom:20px;">

<img
src="<?= $_SESSION['profile_picture'] ? UPLOAD_AVATAR_URL . e($_SESSION['profile_picture']) : DEFAULT_AVATAR ?>"
class="avatar-lg"
style="width:120px;height:120px;border-radius:50%;object-fit:cover;">

</div>

<div class="form-group">
<label>Full Name</label>
<input type="text" value="<?= e($_SESSION['name']) ?>" disabled>
</div>

<div class="form-group">
<label>Email</label>
<input type="text" value="<?= e($_SESSION['email']) ?>" disabled>
</div>

<div class="form-group">
<label>Role</label>
<input type="text" value="<?= ucfirst($_SESSION['role']) ?>" disabled>
</div>

<div class="form-group">
<label>Profile Picture</label>
<input type="file" name="profile_picture" accept="image/*">
</div>

<button class="btn btn-primary" name="update_admin">
<i class="fa-solid fa-floppy-disk"></i>
Save Changes
</button>

</form>

</div>

</div>

<?php endif; ?>

<div class="card">

<div class="card-header">
<h3><i class="fa-solid fa-lock"></i> Change Password</h3>
</div>

<div class="card-body">

<form method="POST">

<input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

<div class="form-group">
<label>New Password</label>
<input type="password" name="new_password" minlength="6" required>
</div>

<div class="form-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" minlength="6" required>
</div>

<button class="btn btn-primary" name="change_password">
<i class="fa-solid fa-key"></i>
Update Password
</button>

</form>

</div>

</div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>