<?php
require_once 'includes/config.php';
$pageTitle = 'Contact Us';
$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');
    
    if (empty($name)) $errors[] = 'Name required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
    if (empty($subject)) $errors[] = 'Subject required.';
    if (strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';
    
    if (empty($errors)) {
        $pdo = getDB();
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = 'Thank you! Your message has been sent.';
        $_POST = [];
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h4><i class="bi bi-envelope-paper text-primary"></i> Get in Touch</h4>
                    <p class="text-muted">We'd love to hear from you.</p>
                    <hr>
                    <p><i class="bi bi-geo-alt text-primary"></i> Nairobi, Kenya</p>
                    <p><i class="bi bi-envelope text-primary"></i> info@scholarshipportal.co.ke</p>
                    <p><i class="bi bi-phone text-primary"></i> +254 700 000 000</p>
                    <p><i class="bi bi-clock text-primary"></i> Mon-Fri: 8AM - 5PM</p>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h4 class="mb-4">Send Us a Message</h4>
                    <?php if ($success): ?><div class="alert alert-success"><?= sanitize($success) ?></div><?php endif; ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Your Name</label><input type="text" name="name" class="form-control" required value="<?= sanitize($_POST['name'] ?? '') ?>"></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required value="<?= sanitize($_POST['email'] ?? '') ?>"></div>
                            <div class="col-12"><label class="form-label">Subject</label><input type="text" name="subject" class="form-control" required value="<?= sanitize($_POST['subject'] ?? '') ?>"></div>
                            <div class="col-12"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="6" required><?= sanitize($_POST['message'] ?? '') ?></textarea></div>
                            <div class="col-12"><button type="submit" class="btn btn-primary btn-lg w-100"><i class="bi bi-send"></i> Send Message</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>