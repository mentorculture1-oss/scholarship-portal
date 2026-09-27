<?php
require_once 'includes/config.php';
requireLogin();

$pageTitle = 'My Profile';
$user = getCurrentUser();
$pdo = getDB();
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $county = sanitize($_POST['county'] ?? '');
    $institution = sanitize($_POST['institution'] ?? '');
    $course = sanitize($_POST['course'] ?? '');
    $year_of_study = (int)($_POST['year_of_study'] ?? 0);
    
    if (empty($full_name)) $errors[] = 'Full name required.';
    if (!preg_match('/^[0-9]{10,12}$/', $phone)) $errors[] = 'Invalid phone number.';
    if (empty($county) || empty($institution) || empty($course)) $errors[] = 'All fields required.';
    
    $new_password = $_POST['new_password'] ?? '';
    $current_password = $_POST['current_password'] ?? '';
    
    $sql = "UPDATE users SET full_name=?, phone=?, county=?, institution=?, course=?, year_of_study=?";
    $params = [$full_name, $phone, $county, $institution, $course, $year_of_study];
    
    if (!empty($new_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = 'Current password is incorrect.';
        } elseif (strlen($new_password) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } else {
            $sql .= ", password=?";
            $params[] = password_hash($new_password, PASSWORD_BCRYPT);
        }
    }
    
    if (empty($errors)) {
        $sql .= " WHERE user_id=?";
        $params[] = $user['user_id'];
        $pdo->prepare($sql)->execute($params);
        $_SESSION['user_name'] = $full_name;
        $success = 'Profile updated successfully!';
        $user = getCurrentUser();
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-person-circle"></i> My Profile</h2>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <h6 class="text-primary mb-3">Personal Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control" required value="<?= sanitize($user['full_name']) ?>"></div>
                            <div class="col-md-6"><label class="form-label">Email (cannot change)</label><input type="email" class="form-control" disabled value="<?= sanitize($user['email']) ?>"></div>
                            <div class="col-md-6"><label class="form-label">Phone</label><input type="tel" name="phone" class="form-control" required value="<?= sanitize($user['phone']) ?>"></div>
                            <div class="col-md-6"><label class="form-label">National ID (cannot change)</label><input type="text" class="form-control" disabled value="<?= sanitize($user['national_id']) ?>"></div>
                            <div class="col-md-6">
                                <label class="form-label">County</label>
                                <select name="county" class="form-select" required>
                                    <?php foreach (getKenyanCounties() as $c): ?>
                                        <option value="<?= $c ?>" <?= $user['county'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="text-primary mb-3">Academic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6"><label class="form-label">Institution</label><input type="text" name="institution" class="form-control" required value="<?= sanitize($user['institution']) ?>"></div>
                            <div class="col-md-6"><label class="form-label">Course</label><input type="text" name="course" class="form-control" required value="<?= sanitize($user['course']) ?>"></div>
                            <div class="col-md-6">
                                <label class="form-label">Year of Study</label>
                                <select name="year_of_study" class="form-select" required>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?= $i ?>" <?= $user['year_of_study'] == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="text-primary mb-3">Change Password (Optional)</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" minlength="8"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3"><i class="bi bi-person-circle text-primary" style="font-size: 6rem;"></i></div>
                    <h5><?= sanitize($user['full_name']) ?></h5>
                    <p class="text-muted small"><?= sanitize($user['email']) ?></p>
                    <hr>
                    <p class="small mb-1"><strong>Member since:</strong></p>
                    <p class="text-muted small"><?= formatDate($user['created_at']) ?></p>
                    <p class="small mb-1"><strong>Status:</strong></p>
                    <?= getStatusBadge($user['status']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>