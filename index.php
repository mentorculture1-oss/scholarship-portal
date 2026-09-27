<?php
require_once 'includes/config.php';

$pageTitle = 'Home';
$pdo = getDB();

$stmt = $pdo->query("SELECT * FROM scholarships 
    WHERE status = 'open' AND application_deadline >= CURDATE() 
    ORDER BY created_at DESC LIMIT 3");
$featuredScholarships = $stmt->fetchAll();

$totalScholarships = $pdo->query("SELECT COUNT(*) FROM scholarships WHERE status = 'open'")->fetchColumn();
$totalStudents     = $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetchColumn();
$totalApplications = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();

include 'includes/header.php';
?>

<section class="hero-section text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Your Future Starts Here</h1>
                <p class="lead mb-4">Discover and apply for scholarships tailored to your needs.</p>
                <a href="scholarships.php" class="btn btn-light btn-lg">
                    <i class="bi bi-search"></i> Browse Scholarships
                </a>
                <?php if (!isLoggedIn()): ?>
                    <a href="register.php" class="btn btn-outline-light btn-lg ms-2">
                        <i class="bi bi-person-plus"></i> Register Now
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <i class="bi bi-mortarboard-fill" style="font-size: 10rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-award text-primary display-4"></i>
                        <h3 class="mt-3"><?= $totalScholarships ?></h3>
                        <p class="text-muted mb-0">Open Scholarships</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-people text-success display-4"></i>
                        <h3 class="mt-3"><?= $totalStudents ?></h3>
                        <p class="text-muted mb-0">Registered Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-file-earmark-text text-warning display-4"></i>
                        <h3 class="mt-3"><?= $totalApplications ?></h3>
                        <p class="text-muted mb-0">Applications Submitted</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-star-fill text-warning"></i> Featured Scholarships</h2>
            <a href="scholarships.php" class="btn btn-outline-primary">View All</a>
        </div>

        <div class="row">
            <?php if (empty($featuredScholarships)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No scholarships available yet.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($featuredScholarships as $scholarship): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm scholarship-card">
                            <div class="card-body">
                                <span class="badge bg-primary mb-2"><?= sanitize($scholarship['category']) ?></span>
                                <h5 class="card-title"><?= sanitize($scholarship['title']) ?></h5>
                                <p class="card-text text-muted small">
                                    <i class="bi bi-building"></i> <?= sanitize($scholarship['provider']) ?>
                                </p>
                                <p class="card-text"><?= substr(sanitize($scholarship['description']), 0, 100) ?>...</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-success fw-bold"><?= formatCurrency($scholarship['amount']) ?></span>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> <?= formatDate($scholarship['application_deadline']) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="scholarship-details.php?id=<?= $scholarship['scholarship_id'] ?>" class="btn btn-primary w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>