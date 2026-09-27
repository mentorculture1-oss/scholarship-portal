<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pdo = getDB();
$id = (int)($_GET['id'] ?? 0);
$edit = $id > 0;
$pageTitle = $edit ? 'Edit Scholarship' : 'Add Scholarship';
$errors = [];
$s = ['title'=>'','description'=>'','eligibility'=>'','amount'=>'','slots'=>'','category'=>'','provider'=>'','application_deadline'=>'','status'=>'open'];

if ($edit) {
    $stmt = $pdo->prepare("SELECT * FROM scholarships WHERE scholarship_id=?");
    $stmt->execute([$id]);
    $s = $stmt->fetch();
    if (!$s) { setFlash('danger', 'Scholarship not found.'); redirect('scholarships.php'); }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($s as $key => $val) $s[$key] = sanitize($_POST[$key] ?? '');
    if (empty($s['title'])) $errors[] = 'Title required.';
    if (empty($s['description'])) $errors[] = 'Description required.';
    if (empty($s['eligibility'])) $errors[] = 'Eligibility required.';
    if (empty($s['amount']) || $s['amount'] <= 0) $errors[] = 'Valid amount required.';
    if (empty($s['category'])) $errors[] = 'Category required.';
    if (empty($s['provider'])) $errors[] = 'Provider required.';
    if (empty($s['application_deadline'])) $errors[] = 'Deadline required.';
    
    if (empty($errors)) {
        if ($edit) {
            $stmt = $pdo->prepare("UPDATE scholarships SET title=?, description=?, eligibility=?, amount=?, slots=?, category=?, provider=?, application_deadline=?, status=? WHERE scholarship_id=?");
            $stmt->execute([$s['title'],$s['description'],$s['eligibility'],$s['amount'],$s['slots'] ?: null,$s['category'],$s['provider'],$s['application_deadline'],$s['status'],$id]);
            setFlash('success', 'Scholarship updated!');
        } else {
            $stmt = $pdo->prepare("INSERT INTO scholarships (title, description, eligibility, amount, slots, category, provider, application_deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$s['title'],$s['description'],$s['eligibility'],$s['amount'],$s['slots'] ?: null,$s['category'],$s['provider'],$s['application_deadline'],$s['status']]);
            notifyAllStudents('New Scholarship Available! 🎓', 'A new scholarship "' . $s['title'] . '" has been added. Apply before ' . formatDate($s['application_deadline']) . '!', 'info');
            setFlash('success', 'Scholarship added and students notified!');
        }
        redirect(APP_URL . 'admin/scholarships.php');
    }
}

include 'includes/header.php';
?>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Title *</label><input type="text" name="title" class="form-control" required value="<?= sanitize($s['title']) ?>"></div>
                <div class="col-md-4"><label class="form-label">Category *</label>
                    <select name="category" class="form-select" required>
                        <option value="">Select...</option>
                        <?php foreach (getScholarshipCategories() as $c): ?>
                            <option value="<?= $c ?>" <?= $s['category'] === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Provider *</label><input type="text" name="provider" class="form-control" required value="<?= sanitize($s['provider']) ?>"></div>
                <div class="col-md-3"><label class="form-label">Amount (KES) *</label><input type="number" name="amount" step="0.01" class="form-control" required value="<?= sanitize($s['amount']) ?>"></div>
                <div class="col-md-3"><label class="form-label">Slots</label><input type="number" name="slots" class="form-control" value="<?= sanitize($s['slots']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Deadline *</label><input type="date" name="application_deadline" class="form-control" required value="<?= sanitize($s['application_deadline']) ?>"></div>
                <div class="col-md-6"><label class="form-label">Status *</label>
                    <select name="status" class="form-select" required>
                        <option value="open" <?= $s['status'] === 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="closed" <?= $s['status'] === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </div>
                <div class="col-12"><label class="form-label">Description *</label><textarea name="description" class="form-control" rows="4" required><?= sanitize($s['description']) ?></textarea></div>
                <div class="col-12"><label class="form-label">Eligibility Requirements *</label><textarea name="eligibility" class="form-control" rows="4" required><?= sanitize($s['eligibility']) ?></textarea></div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> <?= $edit ? 'Update' : 'Create' ?></button>
                    <a href="scholarships.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>