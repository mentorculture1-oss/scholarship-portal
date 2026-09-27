<?php
require_once __DIR__ . '/../includes/config.php';

if (isAdminLoggedIn()) {
    redirect(APP_URL . 'admin/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    try {
        loginAdmin($username, $password);
        setFlash('success', 'Welcome back, Admin!');
        redirect(APP_URL . 'admin/dashboard.php');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="<?= $_COOKIE['theme'] ?? 'light' ?>">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= APP_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-4 col-md-6">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h3 class="mt-3">Admin Login</h3>
                            <p class="text-muted small">Restricted Access</p>
                        </div>
                        <?php if ($error): ?><div class="alert alert-danger"><?= sanitize($error) ?></div><?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username or Email</label>
                                <input type="text" name="username" class="form-control" required autofocus>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </form>
                        <hr class="my-3">
                        <p class="text-center small text-muted mb-0">
                            <a href="<?= APP_URL ?>" class="text-decoration-none"><i class="bi bi-arrow-left"></i> Back to Portal</a>
                        </p>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3">Default: <code>admin</code> / <code>password</code></p>
            </div>
        </div>
    </div>
</body>
</html>