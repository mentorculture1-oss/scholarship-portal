<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pdo = getDB();
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('applications.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = sanitize($_POST['status'] ?? '');
    $admin_notes = sanitize($_POST['admin_notes'] ?? '');
    $admin = getCurrentAdmin();
    $allowed = ['pending','under_review','shortlisted','awarded','rejected'];
    if (in_array($new_status, $allowed)) {
        $pdo->prepare("UPDATE applications SET status=?, admin_notes=?, reviewed_by=?, reviewed_at=NOW() WHERE application_id=?")
            ->execute([$new_status, $admin_notes, $admin['admin_id'], $id]);
        
        $stmt = $pdo->prepare("SELECT a.user_id, s.title FROM applications a JOIN scholarships s ON a.scholarship_id=s.scholarship_id WHERE a.application_id=?");
        $stmt->execute([$id]);
        $info = $stmt->fetch();
        $msg = "Your application for \"{$info['title']}\" has been updated to: " . ucfirst(str_replace('_',' ',$new_status)) . ".";
        if ($admin_notes) $msg .= " Note: " . $admin_notes;
        sendNotification($info['user_id'], 'Application Status Updated', $msg, 
            $new_status === 'awarded' ? 'success' : ($new_status === 'rejected' ? 'danger' : 'info'));
        setFlash('success', 'Application status updated and student notified.');
        redirect('application-view.php?id=' . $id);
    }
}

$stmt = $pdo->prepare("SELECT a.*, u.full_name, u.email, u.phone, u.gender, u.date_of_birth, u.national_id, u.county, u.institution, u.course, u.year_of_study, s.title, s.provider, s.amount FROM applications a 
    JOIN users u ON a.user_id=u.user_id 
    JOIN scholarships s ON a.scholarship_id=s.scholarship_id WHERE a.application_id=?");
$stmt->execute([$id]);
$app = $stmt->fetch();
if (!$app) { setFlash('danger', 'Application not found.'); redirect('applications.php'); }

$pageTitle = 'Review Application #' . $id;
include 'includes/header.php';
?>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white"><h5 class="mb-0">Applicant Details</h5></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Name:</strong> <?= sanitize($app['full_name']) ?></div>
                    <div class="col-md-6"><strong>Email:</strong> <?= sanitize($app['email']) ?></div>
                    <div class="col-md-6"><strong>Phone:</strong> <?= sanitize($app['phone']) ?></div>
                    <div class="col-md-6"><strong>National ID:</strong> <?= sanitize($app['national_id']) ?></div>
                    <div class="col-md-6"><strong>Gender:</strong> <?= sanitize($app['gender']) ?></div>
                    <div class="col-md-6"><strong>DOB:</strong> <?= formatDate($app['date_of_birth']) ?></div>
                    <div class="col-md-6"><strong>County:</strong> <?= sanitize($app['county']) ?></div>
                    <div class="col-md-6"><strong>Institution:</strong> <?= sanitize($app['institution']) ?></div>
                    <div class="col-md-6"><strong>Course:</strong> <?= sanitize($app['course']) ?></div>
                    <div class="col-md-6"><strong>Year:</strong> <?= sanitize($app['year_of_study']) ?></div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white"><h5 class="mb-0">Application Details</h5></div>
            <div class="card-body">
                <h6>Personal Statement</h6>
                <p class="text-muted"><?= nl2br(sanitize($app['personal_statement'])) ?></p>
                <hr>
                <h6>Financial Need</h6>
                <p class="text-muted"><?= nl2br(sanitize($app['financial_need'])) ?></p>
                <hr>
                <h6>GPA</h6>
                <p class="text-muted"><?= $app['gpa'] ?: 'Not provided' ?></p>
                <hr>
                <h6>Documents</h6>
                <?php foreach (['Transcript'=>'transcript_path','Supporting Doc'=>'supporting_doc_path','Recommendation'=>'recommendation_path'] as $label => $key): ?>
                    <div class="mb-2"><strong><?= $label ?>:</strong>
                        <?php if ($app[$key]): ?>
                            <a href="<?= APP_URL ?>assets/uploads/<?= $app[$key] ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Download</a>
                        <?php else: ?>
                            <span class="text-muted">Not uploaded</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-primary text-white"><h5 class="mb-0">Review Action</h5></div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3"><label class="form-label">Current Status</label><br><?= getStatusBadge($app['status']) ?></div>
                    <div class="mb-3"><label class="form-label">Update Status *</label>
                        <select name="status" class="form-select" required>
                            <?php foreach (['pending','under_review','shortlisted','awarded','rejected'] as $st): ?>
                                <option value="<?= $st ?>" <?= $app['status'] === $st ? 'selected' : '' ?>><?= ucfirst(str_replace('_',' ',$st)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><label class="form-label">Admin Notes</label><textarea name="admin_notes" class="form-control" rows="3"><?= sanitize($app['admin_notes'] ?? '') ?></textarea></div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-circle"></i> Update Status</button>
                </form>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h6 class="mb-0">Scholarship Info</h6></div>
            <div class="card-body small">
                <p class="mb-1"><strong>Title:</strong> <?= sanitize($app['title']) ?></p>
                <p class="mb-1"><strong>Provider:</strong> <?= sanitize($app['provider']) ?></p>
                <p class="mb-0"><strong>Amount:</strong> <?= formatCurrency($app['amount']) ?></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>