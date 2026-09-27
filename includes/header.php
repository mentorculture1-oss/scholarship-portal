<?php
if (!defined('APP_NAME')) {
    require_once __DIR__ . '/config.php';
}
$currentUser = isLoggedIn() ? getCurrentUser() : null;
$unreadCount = $currentUser ? getUnreadCount($currentUser['user_id']) : 0;
$currentTheme = $_COOKIE['theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $currentTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?><?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= APP_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= APP_URL ?>">
                <i class="bi bi-mortarboard-fill"></i> Scholarship Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>index.php"><i class="bi bi-house"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>scholarships.php"><i class="bi bi-search"></i> Scholarships</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>about.php"><i class="bi bi-info-circle"></i> About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>contact.php"><i class="bi bi-envelope"></i> Contact</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <button class="btn btn-link nav-link" id="themeToggle" title="Toggle theme">
                            <i class="bi bi-<?= $currentTheme === 'dark' ? 'sun' : 'moon' ?>"></i>
                        </button>
                    </li>
                    <?php if ($currentUser): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unreadCount ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center" href="<?= APP_URL ?>notifications.php">View All Notifications</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?= sanitize($currentUser['full_name']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= APP_URL ?>dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>my-applications.php"><i class="bi bi-file-earmark-text"></i> My Applications</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>profile.php"><i class="bi bi-person"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?= APP_URL ?>login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                        <li class="nav-item"><a class="btn btn-light btn-sm ms-2 mt-1" href="<?= APP_URL ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main class="min-vh-100">
        <div class="container mt-3">
            <?php displayFlash(); ?>
        </div>