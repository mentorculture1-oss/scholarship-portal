<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    redirect(APP_URL . 'dashboard.php');
}

$pageTitle = 'Register';
$errors = [];
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST;
    
    $full_name     = sanitize($_POST['full_name'] ?? '');
    $email         = sanitize($_POST['email'] ?? '');
    $phone         = sanitize($_POST['phone'] ?? '');
    $password      = $_POST['password'] ?? '';
    $confirm       = $_POST['confirm_password'] ?? '';
    $gender        = sanitize($_POST['gender'] ?? '');
    $dob           = sanitize($_POST['date_of_birth'] ?? '');
    $national_id   = sanitize($_POST['national_id'] ?? '');
    $county        = sanitize($_POST['county'] ?? '');
    $institution   = sanitize($_POST['institution'] ?? '');
    $course        = sanitize($_POST['course'] ?? '');
    $year_of_study = (int)($_POST['year_of_study'] ?? 0);
    
    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!preg_match('/^[0-9]{10,12}$/', $phone)) $errors[] = 'Valid phone number required (10-12 digits).';
    if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Password must contain an uppercase letter.';
    if (!preg_match('/[a-z]/', $password)) $errors[] = 'Password must contain a lowercase letter.';
    if (!preg_match('/[0-9]/', $password)) $errors[] = 'Password must contain a number.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    if (!in_array($gender, ['Male', 'Female', 'Other'])) $errors[] = 'Please select gender.';
    if (empty($dob)) $errors[] = 'Date of birth is required.';
    if (empty($national_id)) $errors[] = 'National ID is required.';
    if (empty($county)) $errors[] = 'County is required.';
    if (empty($institution)) $errors[] = 'Institution is required.';
    if (empty($course)) $errors[] = 'Course is required.';
    if ($year_of_study < 1 || $year_of_study > 8) $errors[] = 'Year of study must be between 1 and 8.';
    
    if (empty($errors)) {
        try {
            $userId = registerUser([
                'full_name' => $full_name, 'email' => $email, 'phone' => $phone,
                'password' => $password, 'gender' => $gender, 'date_of_birth' => $dob,
                'national_id' => $national_id, 'county' => $county,
                'institution' => $institution, 'course' => $course,
                'year_of_study' => $year_of_study
            ]);
            
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            
            setFlash('success', 'Registration successful! Welcome to the Scholarship Portal.');
            redirect(APP_URL . 'dashboard.php');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-3">Create Your Account</h3>
                        <p class="text-muted">Join thousands of students accessing scholarship opportunities</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= sanitize($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" novalidate>
                        <h6 class="text-primary mb-3"><i class="bi bi-person"></i> Personal Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control" required value="<?= sanitize($old['full_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-control" required value="<?= sanitize($old['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Number *</label>
                                <input type="tel" name="phone" class="form-control" required placeholder="0712345678" value="<?= sanitize($old['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">National ID *</label>
                                <input type="text" name="national_id" class="form-control" required value="<?= sanitize($old['national_id'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender *</label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select...</option>
                                    <option value="Male" <?= ($old['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($old['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= ($old['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" name="date_of_birth" class="form-control" required value="<?= sanitize($old['date_of_birth'] ?? '') ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">County *</label>
                                <select name="county" class="form-select" required>
                                    <option value="">Select County...</option>
                                    <?php foreach (getKenyanCounties() as $c): ?>
                                        <option value="<?= $c ?>" <?= ($old['county'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="text-primary mb-3"><i class="bi bi-mortarboard"></i> Academic Information</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Institution *</label>
                                <input type="text" name="institution" class="form-control" required value="<?= sanitize($old['institution'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course *</label>
                                <input type="text" name="course" class="form-control" required value="<?= sanitize($old['course'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Year of Study *</label>
                                <select name="year_of_study" class="form-select" required>
                                    <option value="">Select...</option>
                                    <?php for ($i = 1; $i <= 8; $i++): ?>
                                        <option value="<?= $i ?>" <?= ($old['year_of_study'] ?? '') == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                        
                        <h6 class="text-primary mb-3"><i class="bi bi-lock"></i> Account Security</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" id="password" class="form-control" required minlength="8" oninput="validatePassword(this)">
                                <small class="text-muted">Min 8 chars, with upper, lower & number</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required oninput="confirmPassword(this, document.getElementById('password'))">
                            </div>
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a></label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="bi bi-person-check"></i> Create Account
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    <p class="text-center mb-0">Already have an account? <a href="login.php" class="fw-bold">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>1. Acceptance of Terms</h6>
                <p>By registering, you agree to provide accurate information.</p>
                <h6>2. Use of Service</h6>
                <p>The portal is for scholarship discovery and application purposes only.</p>
                <h6>3. Privacy</h6>
                <p>Your data is stored securely and used only for scholarship processing.</p>
                <h6>4. Account Responsibility</h6>
                <p>You are responsible for maintaining the confidentiality of your account.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>