<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$pdo = db();
$total = (int)$pdo->query('SELECT COUNT(*) FROM feedback')->fetchColumn();
$users = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();

$byStatus = array_fill_keys(STATUSES, 0);
foreach ($pdo->query('SELECT status, COUNT(*) c FROM feedback GROUP BY status') as $r) $byStatus[$r['status']] = (int)$r['c'];

$byCat = $pdo->query('SELECT c.name, COUNT(f.id) c FROM categories c
    LEFT JOIN feedback f ON f.category_id = c.id GROUP BY c.id, c.name ORDER BY c.name')->fetchAll();

$byPriority = array_fill_keys(PRIORITIES, 0);
foreach ($pdo->query('SELECT priority, COUNT(*) c FROM feedback GROUP BY priority') as $r) $byPriority[$r['priority']] = (int)$r['c'];

$trend = $pdo->query('SELECT DATE_FORMAT(created_at, "%Y-%m") m, COUNT(*) c FROM feedback
    GROUP BY m ORDER BY m DESC LIMIT 6')->fetchAll();
$trend = array_reverse($trend);

$recent = $pdo->query('SELECT f.id, f.title, f.status, f.priority, u.full_name, f.created_at
    FROM feedback f JOIN users u ON u.id = f.user_id ORDER BY f.created_at DESC LIMIT 5')->fetchAll();

$pageTitle = 'Admin Dashboard';
$chartData = [
    'status' => ['labels' => array_keys($byStatus), 'data' => array_values($byStatus)],
    'category' => ['labels' => array_column($byCat, 'name'), 'data' => array_map('intval', array_column($byCat, 'c'))],
    'priority' => ['labels' => array_keys($byPriority), 'data' => array_values($byPriority)],
    'trend' => ['labels' => array_column($trend, 'm'), 'data' => array_map('intval', array_column($trend, 'c'))],
];
$extraScripts = '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>'
    . '<script>window.CHART_DATA = ' . json_encode($chartData) . ';</script>'
    . '<script src="' . url('assets/js/charts.js') . '"></script>';
include __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">Admin Dashboard</h3>
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3"><div class="card stat-card p-3 text-center"><div class="stat-num"><?= $total ?></div><div class="text-muted small">Total Feedback</div></div></div>
  <div class="col-6 col-md-3"><div class="card stat-card p-3 text-center"><div class="stat-num"><?= $byStatus['Pending'] ?></div><div class="text-muted small">Pending</div></div></div>
  <div class="col-6 col-md-3"><div class="card stat-card p-3 text-center"><div class="stat-num"><?= $byStatus['Resolved'] + $byStatus['Closed'] ?></div><div class="text-muted small">Resolved / Closed</div></div></div>
  <div class="col-6 col-md-3"><div class="card stat-card p-3 text-center"><div class="stat-num"><?= $users ?></div><div class="text-muted small">Community Members</div></div></div>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-6"><div class="card p-3"><h6>Feedback by Status</h6><canvas id="statusChart" height="200"></canvas></div></div>
  <div class="col-md-6"><div class="card p-3"><h6>Feedback by Category</h6><canvas id="categoryChart" height="200"></canvas></div></div>
  <div class="col-md-6"><div class="card p-3"><h6>Feedback by Priority</h6><canvas id="priorityChart" height="200"></canvas></div></div>
  <div class="col-md-6"><div class="card p-3"><h6>Monthly Trend</h6><canvas id="trendChart" height="200"></canvas></div></div>
</div>
<div class="card p-3">
  <div class="d-flex justify-content-between"><h6>Recent Submissions</h6><a href="<?= url('admin/feedback.php') ?>" class="small">View all</a></div>
  <div class="table-responsive"><table class="table align-middle mb-0">
    <thead><tr><th>Title</th><th>From</th><th>Priority</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($recent as $r): ?>
      <tr><td><?= e($r['title']) ?></td><td><?= e($r['full_name']) ?></td>
        <td><?= priority_badge($r['priority']) ?></td><td><?= status_badge($r['status']) ?></td>
        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('admin/review.php?id=' . $r['id']) ?>">Review</a></td></tr>
    <?php endforeach; ?>
    <?php if (!$recent): ?><tr><td colspan="5" class="text-center text-muted py-3">No submissions yet.</td></tr><?php endif; ?>
    </tbody></table></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
