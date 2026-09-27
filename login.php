<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    redirect(APP_URL . 'dashboard.php');
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    try {
        loginUser($email, $password);
        setFlash('success', 'Welcome back, ' . $_SESSION['user_name'] . '!');
        redirect(APP_URL . 'dashboard.php');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Welcome Back</h3>
                        <p class="text-muted">Login to access your scholarship dashboard</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= sanitize($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="loginPassword" class="form-control" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword', this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    <p class="text-center mb-0">Don't have an account? <a href="register.php" class="fw-bold">Register here</a></p>
                    <p class="text-center mt-2"><a href="admin/login.php" class="text-muted small"><i class="bi bi-shield-check"></i> Admin Login</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, btn) {
    const field = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>

<?php include 'includes/footer.php'; ?>