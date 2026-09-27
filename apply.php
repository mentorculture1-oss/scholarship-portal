<?php
require_once 'includes/config.php';
requireLogin();

$user = getCurrentUser();

if ($user['status'] === 'suspended') {
    setFlash('danger', 'Your account is suspended. You cannot apply.');
    redirect(APP_URL . 'dashboard.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(APP_URL . 'scholarships.php');

$pdo = getDB();
$stmt = $pdo->prepare("SELECT * FROM scholarships WHERE scholarship_id = ? AND status = 'open'");
$stmt->execute([$id]);
$s = $stmt->fetch();

if (!$s) {
    setFlash('danger', 'Scholarship not available.');
    redirect(APP_URL . 'scholarships.php');
}

if (isDeadlinePassed($s['application_deadline'])) {
    setFlash('warning', 'The deadline for this scholarship has passed.');
    redirect(APP_URL . 'scholarships.php');
}

$stmt = $pdo->prepare("SELECT application_id FROM applications WHERE user_id = ? AND scholarship_id = ?");
$stmt->execute([$user['user_id'], $id]);
if ($stmt->fetch()) {
    setFlash('warning', 'You have already applied for this scholarship.');
    redirect(APP_URL . 'my-applications.php');
}

$pageTitle = 'Apply - ' . $s['title'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $personal_statement = sanitize($_POST['personal_statement'] ?? '');
    $financial_need = sanitize($_POST['financial_need'] ?? '');
    $gpa = !empty($_POST['gpa']) ? (float)$_POST['gpa'] : null;
    
    if (strlen($personal_statement) < 100) $errors[] = 'Personal statement must be at least 100 characters.';
    if (strlen($financial_need) < 50) $errors[] = 'Financial need statement must be at least 50 characters.';
    if ($gpa !== null && ($gpa < 0 || $gpa > 4.0)) $errors[] = 'GPA must be between 0 and 4.0.';
    
    if (empty($errors)) {
        try {
            $transcript = null; $supporting = null; $recommendation = null;
            if (!empty($_FILES['transcript']['name'])) 
                $transcript = uploadFile($_FILES['transcript'], 'transcripts');
            if (!empty($_FILES['supporting_doc']['name'])) 
                $supporting = uploadFile($_FILES['supporting_doc'], 'documents');
            if (!empty($_FILES['recommendation']['name'])) 
                $recommendation = uploadFile($_FILES['recommendation'], 'recommendations');
            
            $stmt = $pdo->prepare("INSERT INTO applications 
                (user_id, scholarship_id, personal_statement, financial_need, gpa, 
                 transcript_path, supporting_doc_path, recommendation_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user['user_id'], $id, $personal_statement, $financial_need, $gpa,
                $transcript, $supporting, $recommendation
            ]);
            
            sendNotification($user['user_id'], 'Application Submitted! ✅',
                'Your application for "' . $s['title'] . '" has been submitted successfully.',
                'success');
            
            setFlash('success', 'Application submitted successfully!');
            redirect(APP_URL . 'application-status.php?id=' . $pdo->lastInsertId());
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="scholarships.php">Scholarships</a></li>
            <li class="breadcrumb-item"><a href="scholarship-details.php?id=<?= $id ?>"><?= sanitize($s['title']) ?></a></li>
            <li class="breadcrumb-item active">Apply</li>
        </ol>
    </nav>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-send"></i> Application Form</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" onsubmit="return validateApplicationForm()">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Personal Statement <span class="text-danger">*</span></label>
                            <textarea name="personal_statement" id="personal_statement" class="form-control" rows="6" required minlength="100"
                                placeholder="Tell us about yourself, your academic achievements, and why you deserve this scholarship..."><?= sanitize($_POST['personal_statement'] ?? '') ?></textarea>
                            <small class="text-muted">Minimum 100 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Financial Need Statement <span class="text-danger">*</span></label>
                            <textarea name="financial_need" id="financial_need" class="form-control" rows="5" required minlength="50"
                                placeholder="Describe your financial situation and why you need this scholarship..."><?= sanitize($_POST['financial_need'] ?? '') ?></textarea>
                            <small class="text-muted">Minimum 50 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">GPA (Optional)</label>
                            <input type="number" name="gpa" step="0.01" min="0" max="4" class="form-control" placeholder="e.g., 3.50" value="<?= sanitize($_POST['gpa'] ?? '') ?>">
                            <small class="text-muted">On a 4.0 scale</small>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3"><i class="bi bi-paperclip"></i> Supporting Documents</h6>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Transcript</label>
                                <input type="file" name="transcript" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewFile(this, 'transcriptPreview')">
                                <small id="transcriptPreview" class="text-muted"></small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Supporting Document</label>
                                <input type="file" name="supporting_doc" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewFile(this, 'supportPreview')">
                                <small id="supportPreview" class="text-muted"></small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Recommendation Letter</label>
                                <input type="file" name="recommendation" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" onchange="previewFile(this, 'recPreview')">
                                <small id="recPreview" class="text-muted"></small>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2"><i class="bi bi-info-circle"></i> Allowed: PDF, DOC, DOCX, JPG, PNG. Max 5MB each.</small>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send-check"></i> Submit Application</button>
                            <a href="scholarship-details.php?id=<?= $id ?>" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-info-circle"></i> Scholarship Summary</h6></div>
                <div class="card-body">
                    <h6><?= sanitize($s['title']) ?></h6>
                    <p class="small text-muted mb-2"><?= sanitize($s['provider']) ?></p>
                    <hr>
                    <p class="mb-1 small"><strong>Category:</strong> <?= sanitize($s['category']) ?></p>
                    <p class="mb-1 small"><strong>Amount:</strong> <?= formatCurrency($s['amount']) ?></p>
                    <p class="mb-1 small"><strong>Slots:</strong> <?= $s['slots'] ?? 'N/A' ?></p>
                    <p class="mb-0 small"><strong>Deadline:</strong> <?= formatDate($s['application_deadline']) ?></p>
                </div>
            </div>
            
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light"><h6 class="mb-0"><i class="bi bi-person-circle"></i> Your Details</h6></div>
                <div class="card-body small">
                    <p class="mb-1"><strong>Name:</strong> <?= sanitize($user['full_name']) ?></p>
                    <p class="mb-1"><strong>Email:</strong> <?= sanitize($user['email']) ?></p>
                    <p class="mb-1"><strong>Phone:</strong> <?= sanitize($user['phone']) ?></p>
                    <p class="mb-1"><strong>Institution:</strong> <?= sanitize($user['institution']) ?></p>
                    <p class="mb-0"><strong>Course:</strong> <?= sanitize($user['course']) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>