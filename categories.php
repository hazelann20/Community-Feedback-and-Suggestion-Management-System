<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) > 60) {
            flash('Category name is required (max 60 characters).', 'danger');
        } else {
            try {
                db()->prepare('INSERT INTO categories (name) VALUES (?)')->execute([$name]);
                flash('Category added.');
            } catch (PDOException $e) { flash('That category already exists.', 'warning'); }
        }
    } elseif ($action === 'delete') {
        try {
            db()->prepare('DELETE FROM categories WHERE id = ?')->execute([(int)$_POST['id']]);
            flash('Category deleted.');
        } catch (PDOException $e) { flash('Cannot delete a category that has feedback.', 'warning'); }
    }
    redirect('admin/categories.php');
}

$cats = db()->query('SELECT c.*, COUNT(f.id) n FROM categories c LEFT JOIN feedback f ON f.category_id = c.id
    GROUP BY c.id ORDER BY c.name')->fetchAll();
$pageTitle = 'Categories';
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-4 mx-auto" style="max-width:560px">
  <h4 class="mb-3">Categories</h4>
  <form method="post" class="d-flex gap-2 mb-3"><?= csrf_field() ?><input type="hidden" name="action" value="add">
    <input name="name" class="form-control" placeholder="New category" maxlength="60" required>
    <button class="btn btn-brand">Add</button></form>
  <ul class="list-group">
  <?php foreach ($cats as $c): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span><?= e($c['name']) ?> <span class="badge bg-light text-dark"><?= (int)$c['n'] ?></span></span>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['id'] ?>">
        <button class="btn btn-sm btn-outline-danger" data-confirm="Delete this category?">Delete</button></form>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
