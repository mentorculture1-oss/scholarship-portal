<?php
require_once 'includes/config.php';
$pageTitle = 'About Us';
include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="mb-4 text-center">About the Online Scholarship Portal</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4><i class="bi bi-bullseye text-primary"></i> Our Mission</h4>
                    <p>To provide a centralized, accessible, and transparent platform that connects Kenyan students with scholarship opportunities, simplifying the application process and empowering educational advancement.</p>
                </div>
            </div>
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-eye text-primary"></i> Our Vision</h5>
                            <p class="mb-0">A Kenya where every deserving student has equal access to educational funding opportunities through digital innovation.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5><i class="bi bi-check2-circle text-primary"></i> Our Values</h5>
                            <ul class="mb-0">
                                <li>Transparency in scholarship processes</li>
                                <li>Accessibility for all students</li>
                                <li>Efficiency through technology</li>
                                <li>Integrity and data security</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4><i class="bi bi-lightbulb text-primary"></i> What We Offer</h4>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6"><i class="bi bi-check-circle-fill text-success"></i> <strong>Centralized Scholarship Listings</strong></div>
                        <div class="col-md-6"><i class="bi bi-check-circle-fill text-success"></i> <strong>Online Applications</strong></div>
                        <div class="col-md-6"><i class="bi bi-check-circle-fill text-success"></i> <strong>Real-Time Tracking</strong></div>
                        <div class="col-md-6"><i class="bi bi-check-circle-fill text-success"></i> <strong>Instant Notifications</strong></div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-body p-4 text-center">
                    <h4>Ready to Get Started?</h4>
                    <p class="text-muted">Join thousands of students already using our platform.</p>
                    <?php if (!isLoggedIn()): ?>
                        <a href="register.php" class="btn btn-primary btn-lg"><i class="bi bi-person-plus"></i> Register Now</a>
                    <?php else: ?>
                        <a href="scholarships.php" class="btn btn-primary btn-lg"><i class="bi bi-search"></i> Browse Scholarships</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>