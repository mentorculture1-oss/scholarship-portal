<?php
require_once 'includes/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$user = getCurrentUser();
$pdo = getDB();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$totalApplications = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$user['user_id']]);
$pendingCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'awarded'");
$stmt->execute([$user['user_id']]);
$awardedCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ? AND status = 'under_review'");
$stmt->execute([$user['user_id']]);
$reviewCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT a.*, s.title, s.provider, s.amount 
    FROM applications a 
    JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
    WHERE a.user_id = ? 
    ORDER BY a.created_at DESC LIMIT 5");
$stmt->execute([$user['user_id']]);
$recentApplications = $stmt->fetchAll();

$notifications = getUserNotifications($user['user_id'], 5);
$unreadCount = getUnreadCount($user['user_id']);

$stmt = $pdo->prepare("SELECT * FROM scholarships 
    WHERE status = 'open' AND application_deadline >= CURDATE() 
    AND scholarship_id NOT IN (SELECT scholarship_id FROM applications WHERE user_id = ?) 
    ORDER BY created_at DESC LIMIT 3");
$stmt->execute([$user['user_id']]);
$recommended = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container py-4">
    <div class="card bg-primary text-white mb-4 border-0">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">Welcome, <?= sanitize($user['full_name']) ?>! 👋</h3>
                    <p class="mb-0 opacity-75">Track your applications and discover new opportunities.</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="scholarships.php" class="btn btn-light">
                        <i class="bi bi-search"></i> Find Scholarships
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Total Applications</p>
                            <h3 class="mb-0"><?= $totalApplications ?></h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Pending</p>
                            <h3 class="mb-0"><?= $pendingCount ?></h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Under Review</p>
                            <h3 class="mb-0"><?= $reviewCount ?></h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Awarded</p>
                            <h3 class="mb-0"><?= $awardedCount ?></h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Applications</h5>
                    <a href="my-applications.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentApplications)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No applications yet.</p>
                            <a href="scholarships.php" class="btn btn-primary btn-sm">Browse Scholarships</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr><th>Scholarship</th><th>Status</th><th>Date</th><th></th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentApplications as $app): ?>
                                        <tr>
                                            <td>
                                                <strong><?= sanitize($app['title']) ?></strong><br>
                                                <small class="text-muted"><?= sanitize($app['provider']) ?></small>
                                            </td>
                                            <td><?= getStatusBadge($app['status']) ?></td>
                                            <td><small><?= formatDate($app['created_at']) ?></small></td>
                                            <td>
                                                <a href="application-status.php?id=<?= $app['application_id'] ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-bell"></i> Notifications
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge bg-danger"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </h5>
                    <a href="notifications.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No notifications</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $n): ?>
                            <div class="notification-item <?= $n['is_read'] ? '' : 'unread' ?> border-bottom">
                                <div class="d-flex justify-content-between">
                                    <strong class="small"><?= sanitize($n['title']) ?></strong>
                                    <small class="text-muted"><?= timeAgo($n['created_at']) ?></small>
                                </div>
                                <p class="mb-0 small text-muted"><?= sanitize(substr($n['message'], 0, 80)) ?>...</p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($recommended)): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-stars text-warning"></i> Recommended For You</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($recommended as $s): ?>
                                <div class="col-md-4">
                                    <div class="card h-100 scholarship-card shadow-sm">
                                        <div class="card-body">
                                            <span class="badge bg-primary mb-2"><?= sanitize($s['category']) ?></span>
                                            <h6><?= sanitize($s['title']) ?></h6>
                                            <p class="small text-muted mb-2"><i class="bi bi-building"></i> <?= sanitize($s['provider']) ?></p>
                                            <p class="text-success fw-bold mb-1"><?= formatCurrency($s['amount']) ?></p>
                                            <small class="text-muted"><i class="bi bi-calendar"></i> Deadline: <?= formatDate($s['application_deadline']) ?></small>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <a href="scholarship-details.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-sm btn-primary w-100">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>