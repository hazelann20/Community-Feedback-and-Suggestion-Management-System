<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();
if (is_admin()) redirect('admin/dashboard.php');

$uid = $_SESSION['user_id'];
$counts = array_fill_keys(STATUSES, 0);
$st = db()->prepare('SELECT status, COUNT(*) c FROM feedback WHERE user_id = ? GROUP BY status');
$st->execute([$uid]);
foreach ($st->fetchAll() as $r) $counts[$r['status']] = (int)$r['c'];

$st = db()->prepare('SELECT f.*, c.name AS category FROM feedback f
    JOIN categories c ON c.id = f.category_id
    WHERE f.user_id = ? ORDER BY f.created_at DESC');
$st->execute([$uid]);
$items = $st->fetchAll();

$pageTitle = 'My Dashboard';
include __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">My Submissions</h3>
  <a href="<?= url('user/submit.php') ?>" class="btn btn-brand"><i class="bi bi-plus-lg"></i> New Feedback</a>
</div>
<div class="row g-3 mb-4">
  <?php foreach ($counts as $label => $n): ?>
  <div class="col-6 col-md-3"><div class="card stat-card p-3 text-center">
    <div class="stat-num"><?= $n ?></div><div class="text-muted small"><?= e($label) ?></div>
  </div></div>
  <?php endforeach; ?>
</div>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead><tr><th>Title</th><th>Category</th><th>Priority</th><th>Status</th><th>Submitted</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($items as $f): ?>
        <tr>
          <td><?= e($f['title']) ?></td>
          <td><?= e($f['category']) ?></td>
          <td><?= priority_badge($f['priority']) ?></td>
          <td><?= status_badge($f['status']) ?></td>
          <td class="small text-muted"><?= e(date('M j, Y', strtotime($f['created_at']))) ?></td>
          <td class="text-end text-nowrap">
            <a class="btn btn-sm btn-outline-primary" href="<?= url('user/view.php?id=' . $f['id']) ?>">View</a>
            <?php if ($f['status'] === 'Pending'): ?>
              <a class="btn btn-sm btn-outline-secondary" href="<?= url('user/edit.php?id=' . $f['id']) ?>">Edit</a>
              <form method="post" action="<?= url('user/delete.php') ?>" class="d-inline">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= $f['id'] ?>">
                <button class="btn btn-sm btn-outline-danger" data-confirm="Delete this submission?">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$items): ?><tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
