<?php
if (!defined('APP_NAME')) require_once __DIR__ . '/../../includes/config.php';
$admin = getCurrentAdmin();
$currentPage = basename($_SERVER['PHP_SELF']);
$currentTheme = $_COOKIE['theme'] ?? 'light';
$pdo = getDB();
$pendingApps = $pdo->query("SELECT COUNT(*) FROM applications WHERE status='pending'")->fetchColumn();
$unreadMsgs = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read=0")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $currentTheme ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Admin - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= APP_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-md-block admin-sidebar p-0">
            <div class="p-3 border-bottom border-secondary">
                <h5 class="text-white mb-0"><i class="bi bi-mortarboard-fill"></i> Scholarship</h5>
                <small class="text-muted">Admin Panel</small>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link <?= in_array($currentPage, ['scholarships.php','scholarship-form.php']) ? 'active' : '' ?>" href="scholarships.php"><i class="bi bi-award"></i> Scholarships</a></li>
                <li class="nav-item"><a class="nav-link <?= in_array($currentPage, ['applications.php','application-view.php']) ? 'active' : '' ?>" href="applications.php"><i class="bi bi-file-earmark-text"></i> Applications
                    <?php if ($pendingApps > 0): ?><span class="badge bg-danger"><?= $pendingApps ?></span><?php endif; ?>
                </a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'students.php' ? 'active' : '' ?>" href="students.php"><i class="bi bi-people"></i> Students</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'notifications.php' ? 'active' : '' ?>" href="notifications.php"><i class="bi bi-bell"></i> Notifications</a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'messages.php' ? 'active' : '' ?>" href="messages.php"><i class="bi bi-envelope"></i> Messages
                    <?php if ($unreadMsgs > 0): ?><span class="badge bg-warning"><?= $unreadMsgs ?></span><?php endif; ?>
                </a></li>
                <li class="nav-item"><a class="nav-link <?= $currentPage === 'reports.php' ? 'active' : '' ?>" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a></li>
                <li class="nav-item mt-3 border-top border-secondary pt-3"><a class="nav-link" href="<?= APP_URL ?>" target="_blank"><i class="bi bi-globe"></i> View Site</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="<?= APP_URL ?>admin/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
            </ul>
        </nav>
        <main class="col-md-10 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 mb-0"><?= $pageTitle ?? 'Dashboard' ?></h1>
                <div class="d-flex align-items-center gap-3">
                    <small class="text-muted"><?= date('l, F j, Y') ?></small>
                    <button class="btn btn-sm btn-link" id="themeToggle">
                        <i class="bi bi-<?= $currentTheme === 'dark' ? 'sun' : 'moon' ?>"></i>
                    </button>
                    <div class="dropdown">
                        <a class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= sanitize($admin['full_name']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="container-fluid px-0">
                <?php displayFlash(); ?>