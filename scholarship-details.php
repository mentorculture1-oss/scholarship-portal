<?php
require_once 'includes/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    setFlash('danger', 'Scholarship not found.');
    redirect(APP_URL . 'scholarships.php');
}

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM scholarships WHERE scholarship_id = ?");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    setFlash('danger', 'Scholarship not found.');
    redirect(APP_URL . 'scholarships.php');
}

$pageTitle = $s['title'];

$hasApplied = false;
if (isLoggedIn()) {
    $user = getCurrentUser();
    $stmt = $pdo->prepare("SELECT application_id FROM applications WHERE user_id = ? AND scholarship_id = ?");
    $stmt->execute([$user['user_id'], $id]);
    $hasApplied = $stmt->fetch() !== false;
}

$isOpen = $s['status'] === 'open' && !isDeadlinePassed($s['application_deadline']);

include 'includes/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="scholarships.php">Scholarships</a></li>
            <li class="breadcrumb-item active"><?= sanitize($s['title']) ?></li>
        </ol>
    </nav>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span class="badge bg-primary"><?= sanitize($s['category']) ?></span>
                        <?php if ($isOpen): ?>
                            <span class="badge bg-success">Open</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Closed</span>
                        <?php endif; ?>
                    </div>
                    
                    <h2><?= sanitize($s['title']) ?></h2>
                    <p class="text-muted"><i class="bi bi-building"></i> <?= sanitize($s['provider']) ?></p>
                    
                    <hr>
                    
                    <h5><i class="bi bi-info-circle"></i> Description</h5>
                    <p><?= nl2br(sanitize($s['description'])) ?></p>
                    
                    <h5 class="mt-4"><i class="bi bi-check2-square"></i> Eligibility Requirements</h5>
                    <p><?= nl2br(sanitize($s['eligibility'])) ?></p>
                    
                    <div class="row mt-4 g-3">
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-cash-coin text-success fs-3"></i>
                                <p class="mb-0 small text-muted">Amount</p>
                                <strong><?= formatCurrency($s['amount']) ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-people text-primary fs-3"></i>
                                <p class="mb-0 small text-muted">Slots</p>
                                <strong><?= $s['slots'] ?? 'N/A' ?></strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 text-center">
                                <i class="bi bi-calendar text-danger fs-3"></i>
                                <p class="mb-0 small text-muted">Deadline</p>
                                <strong><?= formatDate($s['application_deadline']) ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Apply Now</h5>
                </div>
                <div class="card-body">
                    <?php if (!isLoggedIn()): ?>
                        <p class="text-muted">You need to be logged in to apply.</p>
                        <a href="login.php" class="btn btn-primary w-100 mb-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
                        <a href="register.php" class="btn btn-outline-primary w-100">Register</a>
                    <?php elseif ($hasApplied): ?>
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i> You have already applied for this scholarship.
                        </div>
                        <a href="my-applications.php" class="btn btn-primary w-100">View My Applications</a>
                    <?php elseif (!$isOpen): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> This scholarship is closed.
                        </div>
                    <?php else: ?>
                        <p class="text-muted small">Click below to submit your application. Ensure you have all supporting documents ready.</p>
                        <a href="apply.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-send"></i> Apply Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>