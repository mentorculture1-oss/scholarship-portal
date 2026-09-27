<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Send Notifications';
$pdo = getDB();
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipient = $_POST['recipient'] ?? 'all';
    $title = sanitize($_POST['title'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    $type = sanitize($_POST['type'] ?? 'info');
    if ($title && $message) {
        if ($recipient === 'all') {
            $count = notifyAllStudents($title, $message, $type);
            $success = "Notification sent to $count students.";
        } else {
            sendNotification((int)$recipient, $title, $message, $type);
            $success = "Notification sent.";
        }
    }
}

$students = $pdo->query("SELECT user_id, full_name, email FROM users WHERE status='active' ORDER BY full_name")->fetchAll();

include 'includes/header.php';
?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="bi bi-bell"></i> Compose Notification</h5></div>
            <div class="card-body">
                <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Recipient *</label>
                        <select name="recipient" class="form-select" required>
                            <option value="all">📢 All Active Students (Broadcast)</option>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s['user_id'] ?>"><?= sanitize($s['full_name']) ?> (<?= sanitize($s['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required maxlength="200"></div>
                    <div class="mb-3"><label class="form-label">Message *</label><textarea name="message" class="form-control" rows="5" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="info">ℹ️ Info (Blue)</option>
                            <option value="success">✅ Success (Green)</option>
                            <option value="warning">⚠️ Warning (Yellow)</option>
                            <option value="danger">❌ Urgent (Red)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send"></i> Send Notification</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0">Recently Sent</h6></div>
            <div class="card-body p-0">
                <?php $recent = $pdo->query("SELECT n.*, u.full_name FROM notifications n JOIN users u ON n.user_id=u.user_id ORDER BY n.created_at DESC LIMIT 10")->fetchAll(); ?>
                <?php foreach ($recent as $r): ?>
                    <div class="border-bottom p-3">
                        <div class="d-flex justify-content-between">
                            <strong class="small"><?= sanitize($r['title']) ?></strong>
                            <small class="text-muted"><?= timeAgo($r['created_at']) ?></small>
                        </div>
                        <div class="small text-muted">→ <?= sanitize($r['full_name']) ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($recent)): ?><p class="text-center text-muted py-3 mb-0">No notifications yet.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>