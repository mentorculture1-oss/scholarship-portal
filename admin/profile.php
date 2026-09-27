<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Admin Profile';
$admin = getCurrentAdmin();
$pdo = getDB();
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $new_pass = $_POST['new_password'] ?? '';
    $current_pass = $_POST['current_password'] ?? '';
    if (empty($full_name)) $errors[] = 'Name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    $sql = "UPDATE admins SET full_name=?, email=?";
    $params = [$full_name, $email];
    if (!empty($new_pass)) {
        if (!password_verify($current_pass, $admin['password'])) $errors[] = 'Current password incorrect.';
        elseif (strlen($new_pass) < 8) $errors[] = 'Password must be at least 8 chars.';
        else { $sql .= ", password=?"; $params[] = password_hash($new_pass, PASSWORD_BCRYPT); }
    }
    if (empty($errors)) {
        $sql .= " WHERE admin_id=?"; $params[] = $admin['admin_id'];
        $pdo->prepare($sql)->execute($params);
        $_SESSION['admin_name'] = $full_name;
        $success = 'Profile updated!';
        $admin = getCurrentAdmin();
    }
}

include 'includes/header.php';
?>

<div class="row"><div class="col-lg-6">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required value="<?= sanitize($admin['full_name']) ?>"></div>
                <div class="mb-3"><label class="form-label">Username (cannot change)</label><input type="text" class="form-control" disabled value="<?= sanitize($admin['username']) ?>"></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= sanitize($admin['email']) ?>"></div>
                <hr><h6>Change Password (Optional)</h6>
                <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control"></div>
                <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" minlength="8"></div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save</button>
            </form>
        </div>
    </div>
</div></div>

<?php include 'includes/footer.php'; ?>