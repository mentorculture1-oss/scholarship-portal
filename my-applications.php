<?php
require_once 'includes/config.php';
requireLogin();

$pageTitle = 'My Applications';
$user = getCurrentUser();
$pdo = getDB();

$status_filter = sanitize($_GET['status'] ?? '');

$sql = "SELECT a.*, s.title, s.provider, s.amount, s.category 
        FROM applications a 
        JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
        WHERE a.user_id = ?";
$params = [$user['user_id']];

if ($status_filter) {
    $sql .= " AND a.status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY a.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$applications = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-file-earmark-text"></i> My Applications</h2>
    
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item"><a class="nav-link <?= !$status_filter ? 'active' : '' ?>" href="my-applications.php">All</a></li>
        <?php foreach (['pending', 'under_review', 'shortlisted', 'awarded', 'rejected'] as $st): ?>
            <li class="nav-item"><a class="nav-link <?= $status_filter === $st ? 'active' : '' ?>" href="?status=<?= $st ?>"><?= ucfirst(str_replace('_', ' ', $st)) ?></a></li>
        <?php endforeach; ?>
    </ul>
    
    <?php if (empty($applications)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3">No applications found</h4>
            <p class="text-muted">Start applying for scholarships to see them here.</p>
            <a href="scholarships.php" class="btn btn-primary"><i class="bi bi-search"></i> Browse Scholarships</a>
        </div>
    <?php else: ?>
        <div class="row g-3">
            <?php foreach ($applications as $app): ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-1"><?= sanitize($app['title']) ?></h5>
                                    <p class="text-muted mb-2 small">
                                        <i class="bi bi-building"></i> <?= sanitize($app['provider']) ?>
                                        &nbsp;|&nbsp;
                                        <span class="badge bg-secondary"><?= sanitize($app['category']) ?></span>
                                    </p>
                                    <p class="mb-0 small">
                                        <strong>Amount:</strong> <?= formatCurrency($app['amount']) ?>
                                        &nbsp;|&nbsp;
                                        <strong>Applied:</strong> <?= formatDate($app['created_at']) ?>
                                    </p>
                                </div>
                                <div class="col-md-3 text-md-center mt-3 mt-md-0">
                                    <?= getStatusBadge($app['status']) ?>
                                </div>
                                <div class="col-md-3 text-md-end mt-3 mt-md-0">
                                    <a href="application-status.php?id=<?= $app['application_id'] ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>