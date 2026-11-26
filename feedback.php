<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$cat = (int)($_GET['category'] ?? 0);
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$where = []; $params = [];
if ($q !== '') { $where[] = '(f.title LIKE ? OR f.description LIKE ?)'; $params[] = "%$q%"; $params[] = "%$q%"; }
if (in_array($status, STATUSES, true)) { $where[] = 'f.status = ?'; $params[] = $status; }
if (in_array($priority, PRIORITIES, true)) { $where[] = 'f.priority = ?'; $params[] = $priority; }
if ($cat) { $where[] = 'f.category_id = ?'; $params[] = $cat; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) { $where[] = 'DATE(f.created_at) >= ?'; $params[] = $from; }
if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) { $where[] = 'DATE(f.created_at) <= ?'; $params[] = $to; }

$sql = 'SELECT f.*, c.name AS category, u.full_name FROM feedback f
        JOIN categories c ON c.id = f.category_id JOIN users u ON u.id = f.user_id'
     . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY f.created_at DESC';
$st = db()->prepare($sql);
$st->execute($params);
$items = $st->fetchAll();
$cats = get_categories();

$pageTitle = 'All Feedback';
include __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-3">All Feedback</h3>
<form class="card p-3 mb-3" method="get">
  <div class="row g-2">
    <div class="col-md-3"><input name="q" class="form-control" placeholder="Search title or description" value="<?= e($q) ?>"></div>
    <div class="col-md-2"><select name="status" class="form-select"><option value="">Any status</option>
      <?php foreach (STATUSES as $s): ?><option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><select name="priority" class="form-select"><option value="">Any priority</option>
      <?php foreach (PRIORITIES as $p): ?><option value="<?= e($p) ?>" <?= $priority === $p ? 'selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-2"><select name="category" class="form-select"><option value="">Any category</option>
      <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 d-flex gap-2"><input type="date" name="from" class="form-control" value="<?= e($from) ?>" title="From"><input type="date" name="to" class="form-control" value="<?= e($to) ?>" title="To"></div>
  </div>
  <div class="mt-2"><button class="btn btn-brand btn-sm">Filter</button> <a href="<?= url('admin/feedback.php') ?>" class="btn btn-link btn-sm">Reset</a>
    <span class="text-muted small ms-2"><?= count($items) ?> result(s)</span></div>
</form>
<div class="card p-3"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>Title</th><th>From</th><th>Category</th><th>Priority</th><th>Status</th><th>Date</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($items as $f): ?>
    <tr><td><?= e($f['title']) ?></td><td><?= e($f['full_name']) ?></td><td><?= e($f['category']) ?></td>
      <td><?= priority_badge($f['priority']) ?></td><td><?= status_badge($f['status']) ?></td>
      <td class="small text-muted"><?= e(date('M j, Y', strtotime($f['created_at']))) ?></td>
      <td class="text-end text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/review.php?id=' . $f['id']) ?>">Review</a>
        <form method="post" action="<?= url('admin/delete.php') ?>" class="d-inline"><?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= $f['id'] ?>">
          <button class="btn btn-sm btn-outline-danger" data-confirm="Permanently delete this feedback?">Delete</button></form>
      </td></tr>
  <?php endforeach; ?>
  <?php if (!$items): ?><tr><td colspan="7" class="text-center text-muted py-4">No matching feedback.</td></tr><?php endif; ?>
  </tbody></table></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
