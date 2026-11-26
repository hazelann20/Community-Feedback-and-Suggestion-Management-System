<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) redirect('index.php');

$errors = [];
$name = $email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($name === '') $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (strlen($pass) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($pass !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $st = db()->prepare('SELECT id FROM users WHERE email = ?');
        $st->execute([$email]);
        if ($st->fetch()) {
            $errors[] = 'That email is already registered.';
        } else {
            db()->prepare('INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)')
                ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('Account created. Please log in.');
            redirect('auth/login.php');
        }
    }
}

$pageTitle = 'Register';
include __DIR__ . '/../includes/header.php';
?>
<div class="card auth-card p-4">
  <h4 class="mb-3">Create an account</h4>
  <?php foreach ($errors as $err): ?><div class="alert alert-danger py-2"><?= e($err) ?></div><?php endforeach; ?>
  <form method="post" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Full name</label>
      <input name="full_name" class="form-control" value="<?= e($name) ?>" required></div>
    <div class="mb-3"><label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e($email) ?>" required></div>
    <div class="mb-3"><label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" minlength="8" required>
      <div class="form-text">Password must be at least 8 characters.</div></div>
    <div class="mb-3"><label class="form-label">Confirm password</label>
      <input type="password" name="confirm" class="form-control" required></div>
    <button class="btn btn-brand w-100">Register</button>
  </form>
  <p class="text-center small mt-3 mb-0">Already registered? <a href="<?= url('auth/login.php') ?>">Login</a></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
