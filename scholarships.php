<?php
require_once 'includes/config.php';

$pageTitle = 'Browse Scholarships';
$pdo = getDB();

$search = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$sort = sanitize($_GET['sort'] ?? 'newest');

$sql = "SELECT * FROM scholarships WHERE status = 'open'";
$params = [];

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR provider LIKE ?)";
    $term = "%$search%";
    $params[] = $term; $params[] = $term; $params[] = $term;
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

switch ($sort) {
    case 'deadline': $sql .= " ORDER BY application_deadline ASC"; break;
    case 'amount_high': $sql .= " ORDER BY amount DESC"; break;
    case 'amount_low': $sql .= " ORDER BY amount ASC"; break;
    default: $sql .= " ORDER BY created_at DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$scholarships = $stmt->fetchAll();

$categories = getScholarshipCategories();

include 'includes/header.php';
?>

<div class="container py-4">
    <h2 class="mb-4"><i class="bi bi-search"></i> Browse Scholarships</h2>
    <p class="text-muted"><?= count($scholarships) ?> scholarship(s) found</p>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search by title, provider..." value="<?= $search ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="sort" class="form-select">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                        <option value="deadline" <?= $sort === 'deadline' ? 'selected' : '' ?>>Deadline</option>
                        <option value="amount_high" <?= $sort === 'amount_high' ? 'selected' : '' ?>>Amount ↓</option>
                        <option value="amount_low" <?= $sort === 'amount_low' ? 'selected' : '' ?>>Amount ↑</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if (empty($scholarships)): ?>
        <div class="text-center py-5">
            <i class="bi bi-inbox text-muted" style="font-size: 5rem;"></i>
            <h4 class="mt-3">No scholarships found</h4>
            <p class="text-muted">Try adjusting your search filters.</p>
            <a href="scholarships.php" class="btn btn-primary">Clear Filters</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($scholarships as $s): 
                $daysLeft = (strtotime($s['application_deadline']) - time()) / 86400;
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm scholarship-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="badge bg-primary"><?= sanitize($s['category']) ?></span>
                                <?php if ($daysLeft < 7 && $daysLeft > 0): ?>
                                    <span class="badge bg-danger">Closing Soon!</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Open</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title"><?= sanitize($s['title']) ?></h5>
                            <p class="small text-muted mb-2"><i class="bi bi-building"></i> <?= sanitize($s['provider']) ?></p>
                            <p class="card-text small"><?= sanitize(substr($s['description'], 0, 120)) ?>...</p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success fw-bold"><?= formatCurrency($s['amount']) ?></span>
                                <small class="text-muted"><i class="bi bi-people"></i> <?= $s['slots'] ?? 'N/A' ?> slots</small>
                            </div>
                            <small class="text-muted d-block mb-3"><i class="bi bi-calendar"></i> Deadline: <?= formatDate($s['application_deadline']) ?></small>
                            <a href="scholarship-details.php?id=<?= $s['scholarship_id'] ?>" class="btn btn-primary w-100">
                                <i class="bi bi-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>