<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT f.*, c.name AS category FROM feedback f
    JOIN categories c ON c.id = f.category_id WHERE f.id = ? AND f.user_id = ?');
$st->execute([$id, $_SESSION['user_id']]);
$f = $st->fetch();
if (!$f) { flash('Submission not found.', 'danger'); redirect('user/dashboard.php'); }

$pageTitle = $f['title'];
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-4 mx-auto" style="max-width:720px">
  <div class="d-flex justify-content-between align-items-start">
    <h4><?= e($f['title']) ?></h4><?= status_badge($f['status']) ?>
  </div>
  <p class="text-muted small mb-3">
    <?= e($f['category']) ?> &middot; Priority: <?= priority_badge($f['priority']) ?> &middot;
    Submitted <?= e(date('M j, Y g:i A', strtotime($f['created_at']))) ?>
  </p>
  <p style="white-space:pre-wrap"><?= e($f['description']) ?></p>
  <hr>
  <h6>Administrator Response</h6>
  <?php if ($f['admin_response']): ?>
    <div class="response-box" style="white-space:pre-wrap"><?= e($f['admin_response']) ?></div>
    <div class="small text-muted mt-1">Last updated <?= e(date('M j, Y g:i A', strtotime($f['updated_at']))) ?></div>
  <?php else: ?>
    <p class="text-muted">No response yet.</p>
  <?php endif; ?>
  <div class="mt-3"><a href="<?= url('user/dashboard.php') ?>" class="btn btn-outline-secondary btn-sm">&larr; Back</a></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
