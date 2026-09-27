<?php
require_once 'includes/config.php';
requireLogin();

$user = getCurrentUser();
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(APP_URL . 'my-applications.php');

$pdo = getDB();
$stmt = $pdo->prepare("SELECT a.*, s.title, s.provider, s.amount, s.category, s.application_deadline 
    FROM applications a 
    JOIN scholarships s ON a.scholarship_id = s.scholarship_id 
    WHERE a.application_id = ? AND a.user_id = ?");
$stmt->execute([$id, $user['user_id']]);
$app = $stmt->fetch();

if (!$app) {
    setFlash('danger', 'Application not found.');
    redirect(APP_URL . 'my-applications.php');
}

$pageTitle = 'Application Status';
$steps = ['pending', 'under_review', 'shortlisted', 'awarded'];
$currentIndex = array_search($app['status'], $steps);
if ($app['status'] === 'rejected') $currentIndex = -1;

include 'includes/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="my-applications.php">My Applications</a></li>
            <li class="breadcrumb-item active">Status</li>
        </ol>
    </nav>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                    <h3><?= sanitize($app['title']) ?></h3>
                    <p class="text-muted mb-0"><i class="bi bi-building"></i> <?= sanitize($app['provider']) ?></p>
                </div>
                <div class="text-end">
                    <?= getStatusBadge($app['status']) ?>
                    <div class="small text-muted mt-1">Applied: <?= formatDate($app['created_at']) ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-diagram-3"></i> Application Progress</h5></div>
        <div class="card-body">
            <?php if ($app['status'] === 'rejected'): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle"></i> <strong>Application Rejected.</strong>
                    <?php if ($app['admin_notes']): ?><br>Reason: <?= sanitize($app['admin_notes']) ?><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="status-tracker">
                    <?php foreach ($steps as $i => $step): 
                        $isActive = $i <= $currentIndex;
                        $isCompleted = $i < $currentIndex;
                    ?>
                        <div class="status-step <?= $isCompleted ? 'completed' : ($isActive ? 'active' : '') ?>">
                            <div class="circle">
                                <?php if ($isCompleted): ?>
                                    <i class="bi bi-check"></i>
                                <?php else: ?>
                                    <?= $i + 1 ?>
                                <?php endif; ?>
                            </div>
                            <div class="mt-2 text-center small"><?= ucfirst(str_replace('_', ' ', $step)) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="alert alert-info mt-4 mb-0">
                    <i class="bi bi-info-circle"></i>
                    <strong>Current Status:</strong> <?= ucfirst(str_replace('_', ' ', $app['status'])) ?>
                    <?php if ($app['status'] === 'awarded'): ?>
                        <br>🎉 Congratulations! You have been awarded this scholarship.
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row g-3">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-file-text"></i> Your Application</h6></div>
                <div class="card-body">
                    <h6>Personal Statement</h6>
                    <p class="text-muted small"><?= nl2br(sanitize($app['personal_statement'])) ?></p>
                    <hr>
                    <h6>Financial Need Statement</h6>
                    <p class="text-muted small"><?= nl2br(sanitize($app['financial_need'])) ?></p>
                    <hr>
                    <h6>GPA</h6>
                    <p class="text-muted small"><?= $app['gpa'] ?: 'Not provided' ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-paperclip"></i> Documents</h6></div>
                <div class="card-body">
                    <?php 
                    $docs = [
                        'Transcript' => $app['transcript_path'],
                        'Supporting Doc' => $app['supporting_doc_path'],
                        'Recommendation' => $app['recommendation_path']
                    ];
                    ?>
                    <?php foreach ($docs as $label => $path): ?>
                        <div class="mb-2">
                            <strong class="small"><?= $label ?>:</strong><br>
                            <?php if ($path): ?>
                                <a href="<?= APP_URL ?>assets/uploads/<?= $path ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> View File
                                </a>
                            <?php else: ?>
                                <small class="text-muted">Not uploaded</small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-white"><h6 class="mb-0"><i class="bi bi-cash"></i> Scholarship Info</h6></div>
                <div class="card-body small">
                    <p class="mb-1"><strong>Amount:</strong> <?= formatCurrency($app['amount']) ?></p>
                    <p class="mb-0"><strong>Category:</strong> <?= sanitize($app['category']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>