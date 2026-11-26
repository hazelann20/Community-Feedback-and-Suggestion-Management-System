<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT f.*, c.name AS category, u.full_name, u.email FROM feedback f
    JOIN categories c ON c.id = f.category_id JOIN users u ON u.id = f.user_id WHERE f.id = ?');
$st->execute([$id]);
$f = $st->fetch();
if (!$f) { flash('Feedback not found.', 'danger'); redirect('admin/feedback.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $newStatus = $_POST['status'] ?? '';
    $response = trim($_POST['admin_response'] ?? '');
    if (!in_array($newStatus, STATUSES, true)) {
        flash('Invalid status.', 'danger');
    } else {
        db()->prepare('UPDATE feedback SET status = ?, admin_response = ? WHERE id = ?')
            ->execute([$newStatus, $response !== '' ? $response : null, $id]);
        flash('Submission updated.');
        redirect('admin/review.php?id=' . $id);
    }
}

$pageTitle = 'Review Feedback';
include __DIR__ . '/../includes/header.php';
?>
<div class="row g-3">
  <div class="col-lg-7"><div class="card p-4">
    <div class="d-flex justify-content-between"><h4><?= e($f['title']) ?></h4><?= status_badge($f['status']) ?></div>
    <p class="text-muted small">
      By <?= e($f['full_name']) ?> (<?= e($f['email']) ?>) &middot; <?= e($f['category']) ?> &middot;
      <?= priority_badge($f['priority']) ?> &middot; <?= e(date('M j, Y g:i A', strtotime($f['created_at']))) ?>
    </p>
    <p style="white-space:pre-wrap"><?= e($f['description']) ?></p>
  </div></div>
  <div class="col-lg-5"><div class="card p-4">
    <h5>Process Submission</h5>
    <form method="post"><?= csrf_field() ?>
      <div class="mb-3"><label class="form-label">Status</label>
        <select name="status" class="form-select">
          <?php foreach (STATUSES as $s): ?><option value="<?= e($s) ?>" <?= $f['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?>
        </select></div>
      <div class="mb-3"><label class="form-label">Response to user</label>
        <textarea name="admin_response" rows="6" class="form-control"><?= e($f['admin_response']) ?></textarea></div>
      <button class="btn btn-brand">Save</button>
      <a href="<?= url('admin/feedback.php') ?>" class="btn btn-link">Back</a>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
