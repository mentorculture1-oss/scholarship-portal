<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Manage Students';
$pdo = getDB();
$admin = getCurrentAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $uid = (int)($_POST['user_id'] ?? 0);
    if ($action === 'suspend' && $uid) {
        $reason = sanitize($_POST['reason'] ?? 'No reason provided');
        $pdo->prepare("UPDATE users SET status='suspended', suspension_reason=?, suspended_at=NOW(), suspended_by=? WHERE user_id=?")
            ->execute([$reason, $admin['admin_id'], $uid]);
        sendNotification($uid, 'Account Suspended', "Your account has been suspended. Reason: $reason", 'danger');
        setFlash('warning', 'Student suspended.');
    } elseif ($action === 'activate' && $uid) {
        $pdo->prepare("UPDATE users SET status='active', suspension_reason=NULL, suspended_at=NULL, suspended_by=NULL WHERE user_id=?")->execute([$uid]);
        sendNotification($uid, 'Account Reactivated', 'Your account has been reactivated.', 'success');
        setFlash('success', 'Student activated.');
    } elseif ($action === 'delete' && $uid) {
        $pdo->prepare("DELETE FROM users WHERE user_id=?")->execute([$uid]);
        setFlash('danger', 'Student deleted.');
    }
    redirect('students.php');
}

$search = sanitize($_GET['search'] ?? '');
$sql = "SELECT u.*, (SELECT COUNT(*) FROM applications WHERE user_id=u.user_id) as apps FROM users u";
$params = [];
if ($search) { $sql .= " WHERE u.full_name LIKE ? OR u.email LIKE ? OR u.national_id LIKE ?"; $t = "%$search%"; $params = [$t,$t,$t]; }
$sql .= " ORDER BY u.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <form method="GET" class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, ID..." value="<?= $search ?>">
            <button class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Email</th><th>Institution</th><th>Apps</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $st): ?>
                        <tr>
                            <td><strong><?= sanitize($st['full_name']) ?></strong><br><small class="text-muted">ID: <?= sanitize($st['national_id']) ?></small></td>
                            <td><?= sanitize($st['email']) ?></td>
                            <td><?= sanitize($st['institution']) ?><br><small class="text-muted"><?= sanitize($st['course']) ?></small></td>
                            <td><span class="badge bg-info"><?= $st['apps'] ?></span></td>
                            <td><?= getStatusBadge($st['status']) ?></td>
                            <td>
                                <?php if ($st['status'] === 'active'): ?>
                                    <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#suspendModal<?= $st['user_id'] ?>"><i class="bi bi-pause"></i> Suspend</button>
                                <?php else: ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="user_id" value="<?= $st['user_id'] ?>">
                                        <button name="action" value="activate" class="btn btn-sm btn-outline-success"><i class="bi bi-play"></i> Activate</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete permanently?')">
                                    <input type="hidden" name="user_id" value="<?= $st['user_id'] ?>">
                                    <button name="action" value="delete" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                <div class="modal fade" id="suspendModal<?= $st['user_id'] ?>" tabindex="-1">
                                    <div class="modal-dialog"><div class="modal-content"><form method="POST">
                                        <div class="modal-header"><h5 class="modal-title">Suspend Student</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                        <div class="modal-body">
                                            <input type="hidden" name="user_id" value="<?= $st['user_id'] ?>">
                                            <p>Suspend <strong><?= sanitize($st['full_name']) ?></strong>?</p>
                                            <label class="form-label">Reason *</label>
                                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button name="action" value="suspend" class="btn btn-warning">Suspend</button>
                                        </div>
                                    </form></div></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($students)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>