<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Manage Scholarships';
$pdo = getDB();

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM scholarships WHERE scholarship_id=?")->execute([(int)$_GET['delete']]);
    setFlash('success', 'Scholarship deleted.');
    redirect(APP_URL . 'admin/scholarships.php');
}

$scholarships = $pdo->query("SELECT s.*, 
    (SELECT COUNT(*) FROM applications WHERE scholarship_id=s.scholarship_id) as app_count 
    FROM scholarships s ORDER BY created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">All Scholarships (<?= count($scholarships) ?>)</h5>
    <a href="scholarship-form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add New</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Title</th><th>Provider</th><th>Category</th><th>Amount</th><th>Deadline</th><th>Apps</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($scholarships as $s): ?>
                        <tr>
                            <td><strong><?= sanitize($s['title']) ?></strong></td>
                            <td><?= sanitize($s['provider']) ?></td>
                            <td><span class="badge bg-secondary"><?= sanitize($s['category']) ?></span></td>
                            <td><?= formatCurrency($s['amount']) ?></td>
                            <td><small><?= formatDate($s['application_deadline']) ?></small></td>
                            <td><span class="badge bg-info"><?= $s['app_count'] ?></span></td>
                            <td><?= getStatusBadge($s['status']) ?></td>
                            <td>
                                <a href="scholarship-form.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <a href="?delete=<?= $s['scholarship_id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this scholarship and all its applications?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($scholarships)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No scholarships yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>