<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Applications';
$pdo = getDB();
$status = sanitize($_GET['status'] ?? '');
$sql = "SELECT a.*, u.full_name, u.email, s.title FROM applications a 
    JOIN users u ON a.user_id = u.user_id 
    JOIN scholarships s ON a.scholarship_id = s.scholarship_id";
$params = [];
if ($status) { $sql .= " WHERE a.status = ?"; $params[] = $status; }
$sql .= " ORDER BY a.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

include 'includes/header.php';
?>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item"><a class="nav-link <?= !$status ? 'active' : '' ?>" href="applications.php">All</a></li>
    <?php foreach (['pending','under_review','shortlisted','awarded','rejected'] as $st): ?>
        <li class="nav-item"><a class="nav-link <?= $status === $st ? 'active' : '' ?>" href="?status=<?= $st ?>"><?= ucfirst(str_replace('_',' ',$st)) ?></a></li>
    <?php endforeach; ?>
</ul>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Student</th><th>Scholarship</th><th>Status</th><th>Applied</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($applications as $a): ?>
                        <tr>
                            <td><?= $a['application_id'] ?></td>
                            <td><strong><?= sanitize($a['full_name']) ?></strong><br><small class="text-muted"><?= sanitize($a['email']) ?></small></td>
                            <td><?= sanitize($a['title']) ?></td>
                            <td><?= getStatusBadge($a['status']) ?></td>
                            <td><small><?= formatDate($a['created_at']) ?></small></td>
                            <td><a href="application-view.php?id=<?= $a['application_id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i> Review</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($applications)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No applications found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>