<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Dashboard';
$pdo = getDB();

$totalStudents     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeStudents    = $pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
$totalScholarships = $pdo->query("SELECT COUNT(*) FROM scholarships")->fetchColumn();
$openScholarships  = $pdo->query("SELECT COUNT(*) FROM scholarships WHERE status='open'")->fetchColumn();
$totalApplications = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$pendingApps       = $pdo->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetchColumn();
$awardedApps       = $pdo->query("SELECT COUNT(*) FROM applications WHERE status='awarded'")->fetchColumn();
$unreadMessages    = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();

$recentApps = $pdo->query("SELECT a.*, u.full_name, s.title 
    FROM applications a 
    JOIN users u ON a.user_id = u.user_id 
    JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
    ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

include 'includes/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Students</p>
                        <h3 class="mb-0"><?= $totalStudents ?></h3>
                        <small class="text-success"><i class="bi bi-check"></i> <?= $activeStudents ?> active</small>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-people"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Scholarships</p>
                        <h3 class="mb-0"><?= $totalScholarships ?></h3>
                        <small class="text-success"><i class="bi bi-check"></i> <?= $openScholarships ?> open</small>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-award"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Applications</p>
                        <h3 class="mb-0"><?= $totalApplications ?></h3>
                        <small class="text-warning"><i class="bi bi-hourglass"></i> <?= $pendingApps ?> pending</small>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-file-earmark-text"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Awarded</p>
                        <h3 class="mb-0"><?= $awardedApps ?></h3>
                        <small class="text-info"><i class="bi bi-envelope"></i> <?= $unreadMessages ?> messages</small>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-trophy"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Applications</h5>
                <a href="applications.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentApps)): ?>
                    <p class="text-muted text-center py-4 mb-0">No applications yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light"><tr><th>Student</th><th>Scholarship</th><th>Status</th><th>Date</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($recentApps as $app): ?>
                                    <tr>
                                        <td><?= sanitize($app['full_name']) ?></td>
                                        <td><?= sanitize(substr($app['title'], 0, 30)) ?></td>
                                        <td><?= getStatusBadge($app['status']) ?></td>
                                        <td><small><?= formatDate($app['created_at']) ?></small></td>
                                        <td><a href="application-view.php?id=<?= $app['application_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Review</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="scholarship-form.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Scholarship</a>
                    <a href="applications.php?status=pending" class="btn btn-warning"><i class="bi bi-hourglass"></i> Review Pending (<?= $pendingApps ?>)</a>
                    <a href="students.php" class="btn btn-info"><i class="bi bi-people"></i> Manage Students</a>
                    <a href="notifications.php" class="btn btn-success"><i class="bi bi-bell"></i> Send Notification</a>
                    <a href="reports.php" class="btn btn-secondary"><i class="bi bi-bar-chart"></i> View Reports</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>