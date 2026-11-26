<?php /* Shared form partial for submit.php and edit.php. Expects $data, $errors, $cats, $buttonLabel */ ?>
<?php foreach ($errors as $err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endforeach; ?>
<form method="post" class="needs-validation" novalidate>
  <?= csrf_field() ?>
  <div class="mb-3"><label class="form-label">Title</label>
    <input name="title" maxlength="150" class="form-control" value="<?= e($data['title']) ?>" required></div>
  <div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Category</label>
      <select name="category_id" class="form-select" required>
        <option value="">Select...</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (int)$data['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Priority</label>
      <select name="priority" class="form-select" required>
        <?php foreach (PRIORITIES as $p): ?>
          <option value="<?= e($p) ?>" <?= $data['priority'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
        <?php endforeach; ?>
      </select></div>
  </div>
  <div class="mb-3"><label class="form-label">Description</label>
    <textarea id="description" name="description" rows="6" minlength="10" class="form-control" required><?= e($data['description']) ?></textarea>
    <div id="desc-count" class="form-text"></div></div>
  <button class="btn btn-brand"><?= e($buttonLabel) ?></button>
  <a href="<?= url('user/dashboard.php') ?>" class="btn btn-link">Cancel</a>
</form>
