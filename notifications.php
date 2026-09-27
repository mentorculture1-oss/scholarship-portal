<?php
require_once 'includes/config.php';
requireLogin();

$pageTitle = 'Notifications';
$user = getCurrentUser();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $nid = (int)($_POST['notification_id'] ?? 0);
    if ($action === 'read' && $nid) markAsRead($nid, $user['user_id']);
    elseif ($action === 'done' && $nid) markAsDone($nid, $user['user_id']);
    elseif ($action === 'clear' && $nid) clearNotification($nid, $user['user_id']);
    elseif ($action === 'clear_all') clearAllNotifications($user['user_id']);
    elseif ($action === 'mark_all_read') {
        $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?")->execute([$user['user_id']]);
    }
    redirect(APP_URL . 'notifications.php');
}

$filter = sanitize($_GET['filter'] ?? 'all');
$sql = "SELECT * FROM notifications WHERE user_id = ?";
$params = [$user['user_id']];
if ($filter === 'unread') $sql .= " AND is_read = 0";
elseif ($filter === 'done') $sql .= " AND is_done = 1";
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$notifications = $stmt->fetchAll();
$unreadCount = getUnreadCount($user['user_id']);

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="mb-0">
            <i class="bi bi-bell"></i> Notifications
            <?php if ($unreadCount > 0): ?><span class="badge bg-danger"><?= $unreadCount ?> unread</span><?php endif; ?>
        </h2>
        <div class="mt-2 mt-md-0">
            <form method="POST" class="d-inline">
                <button name="action" value="mark_all_read" class="btn btn-sm btn-outline-primary"><i class="bi bi-check-all"></i> Mark all read</button>
            </form>
            <form method="POST" class="d-inline" onsubmit="return confirm('Clear ALL notifications?')">
                <button name="action" value="clear_all" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Clear all</button>
            </form>
        </div>
    </div>
    
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link <?= $filter === 'all' ? 'active' : '' ?>" href="?filter=all">All</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter === 'unread' ? 'active' : '' ?>" href="?filter=unread">Unread</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter === 'done' ? 'active' : '' ?>" href="?filter=done">Done</a></li>
    </ul>
    
    <?php if (empty($notifications)): ?>
        <div class="text-center py-5">
            <i class="bi bi-bell-slash text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3">No notifications</h4>
            <p class="text-muted">You're all caught up!</p>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <?php foreach ($notifications as $n): ?>
                <div class="notification-item <?= $n['type'] ?> <?= $n['is_read'] ? '' : 'unread' ?> border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">
                                    <?php if (!$n['is_read']): ?><span class="badge bg-primary">New</span><?php endif; ?>
                                    <?= sanitize($n['title']) ?>
                                    <?php if ($n['is_done']): ?><span class="badge bg-success">Done</span><?php endif; ?>
                                </h6>
                                <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
                            </div>
                            <p class="mb-2 small text-muted"><?= nl2br(sanitize($n['message'])) ?></p>
                            <div class="d-flex gap-1 flex-wrap">
                                <?php if (!$n['is_read']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="notification_id" value="<?= $n['notification_id'] ?>">
                                        <button name="action" value="read" class="btn btn-sm btn-outline-secondary"><i class="bi bi-check"></i> Mark read</button>
                                    </form>
                                <?php endif; ?>
                                <?php if (!$n['is_done']): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="notification_id" value="<?= $n['notification_id'] ?>">
                                        <button name="action" value="done" class="btn btn-sm btn-outline-success"><i class="bi bi-check-all"></i> Mark done</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Remove this notification?')">
                                    <input type="hidden" name="notification_id" value="<?= $n['notification_id'] ?>">
                                    <button name="action" value="clear" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Clear</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>