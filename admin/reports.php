<?php
require_once __DIR__ . '/../includes/config.php';
requireAdminLogin();

$pageTitle = 'Reports & Analytics';
$pdo = getDB();

$statusData = $pdo->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status")->fetchAll();
$categoryData = $pdo->query("SELECT s.category, COUNT(a.application_id) as count FROM scholarships s LEFT JOIN applications a ON s.scholarship_id=a.scholarship_id GROUP BY s.category")->fetchAll();
$monthlyApps = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM applications GROUP BY month ORDER BY month DESC LIMIT 6")->fetchAll();

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalScholarships = $pdo->query("SELECT COUNT(*) FROM scholarships")->fetchColumn();
$totalApps = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$awarded = $pdo->query("SELECT COUNT(*) FROM applications WHERE status='awarded'")->fetchColumn();

include 'includes/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-center border-0 shadow-sm"><div class="card-body"><h4><?= $totalUsers ?></h4><small class="text-muted">Total Users</small></div></div></div>
    <div class="col-md-3"><div class="card text-center border-0 shadow-sm"><div class="card-body"><h4><?= $totalScholarships ?></h4><small class="text-muted">Scholarships</small></div></div></div>
    <div class="col-md-3"><div class="card text-center border-0 shadow-sm"><div class="card-body"><h4><?= $totalApps ?></h4><small class="text-muted">Applications</small></div></div></div>
    <div class="col-md-3"><div class="card text-center border-0 shadow-sm"><div class="card-body"><h4 class="text-success"><?= $awarded ?></h4><small class="text-muted">Awarded</small></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header bg-white"><h6 class="mb-0">Application Status Distribution</h6></div><div class="card-body"><canvas id="statusChart" height="200"></canvas></div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header bg-white"><h6 class="mb-0">Applications by Category</h6></div><div class="card-body"><canvas id="categoryChart" height="200"></canvas></div></div></div>
    <div class="col-12"><div class="card shadow-sm"><div class="card-header bg-white"><h6 class="mb-0">Monthly Applications Trend</h6></div><div class="card-body"><canvas id="monthlyChart" height="80"></canvas></div></div></div>
</div>

<script>
const statusData = <?= json_encode($statusData) ?>;
const categoryData = <?= json_encode($categoryData) ?>;
const monthlyData = <?= json_encode(array_reverse($monthlyApps)) ?>;

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: statusData.map(d => d.status.replace('_', ' ').toUpperCase()), datasets: [{ data: statusData.map(d => d.count), backgroundColor: ['#ffc107','#17a2b8','#0d6efd','#198754','#dc3545','#6c757d'] }] }
});

new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: { labels: categoryData.map(d => d.category), datasets: [{ label: 'Applications', data: categoryData.map(d => d.count), backgroundColor: '#0d6efd' }] },
    options: { plugins: { legend: { display: false } } }
});

new Chart(document.getElementById('monthlyChart'), {
    type: 'line',
    data: { labels: monthlyData.map(d => d.month), datasets: [{ label: 'Applications', data: monthlyData.map(d => d.count), borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', tension: 0.4, fill: true }] }
});
</script>

<?php include 'includes/footer.php'; ?>