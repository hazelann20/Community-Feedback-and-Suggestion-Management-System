<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$st = db()->prepare('SELECT * FROM feedback WHERE id = ? AND user_id = ?');
$st->execute([$id, $_SESSION['user_id']]);
$row = $st->fetch();
if (!$row) { flash('Submission not found.', 'danger'); redirect('user/dashboard.php'); }
if ($row['status'] !== 'Pending') { flash('Only Pending submissions can be edited.', 'warning'); redirect('user/dashboard.php'); }

$cats = get_categories();
$errors = [];
$data = $row;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$errors, $data] = validate_feedback($_POST);
    if (!$errors) {
        db()->prepare('UPDATE feedback SET title=?, category_id=?, priority=?, description=? WHERE id=? AND user_id=?')
            ->execute([$data['title'], $data['category_id'], $data['priority'], $data['description'], $id, $_SESSION['user_id']]);
        flash('Submission updated.');
        redirect('user/dashboard.php');
    }
}

$pageTitle = 'Edit Feedback';
$buttonLabel = 'Save Changes';
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-4 mx-auto" style="max-width:720px">
  <h4 class="mb-3">Edit Submission</h4>
  <?php include __DIR__ . '/_form.php'; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
