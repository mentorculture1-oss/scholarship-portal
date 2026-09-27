<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Contact Messages';
$pdo = getDB();

if (isset($_GET['read'])) { $pdo->prepare("UPDATE contact_messages SET is_read=1 WHERE message_id=?")->execute([(int)$_GET['read']]); redirect('messages.php'); }
if (isset($_GET['delete'])) { $pdo->prepare("DELETE FROM contact_messages WHERE message_id=?")->execute([(int)$_GET['delete']]); setFlash('success', 'Message deleted.'); redirect('messages.php'); }

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-header bg-white"><h5 class="mb-0">Inbox (<?= count($messages) ?>)</h5></div>
    <div class="card-body p-0">
        <?php foreach ($messages as $m): ?>
            <div class="border-bottom p-3 <?= $m['is_read'] ? '' : 'bg-light' ?>">
                <h6 class="mb-1"><?= sanitize($m['subject']) ?> <?php if (!$m['is_read']): ?><span class="badge bg-primary">New</span><?php endif; ?></h6>
                <p class="small text-muted mb-1"><strong><?= sanitize($m['name']) ?></strong> &lt;<?= sanitize($m['email']) ?>&gt; • <?= timeAgo($m['created_at']) ?></p>
                <p class="mb-2"><?= nl2br(sanitize($m['message'])) ?></p>
                <div>
                    <?php if (!$m['is_read']): ?><a href="?read=<?= $m['message_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-check"></i> Mark Read</a><?php endif; ?>
                    <a href="mailto:<?= sanitize($m['email']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-reply"></i> Reply</a>
                    <a href="?delete=<?= $m['message_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this message?')"><i class="bi bi-trash"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><p class="text-center text-muted py-5 mb-0">No messages.</p><?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>