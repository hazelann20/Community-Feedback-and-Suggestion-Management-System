<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_logged_in()) redirect('index.php');

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $pass = $_POST['password'] ?? '';

    $st = db()->prepare('SELECT * FROM users WHERE email = ?');
    $st->execute([$email]);
    $user = $st->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php');
    }
    $error = 'Invalid email or password.';
}

$pageTitle = 'Login';
include __DIR__ . '/../includes/header.php';
?>
<div class="card auth-card p-4">
  <h4 class="mb-3">Login</h4>
  <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="needs-validation" novalidate>
    <?= csrf_field() ?>
    <div class="mb-3"><label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= e($email) ?>" required></div>
    <div class="mb-3"><label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-brand w-100">Login</button>
  </form>
  <p class="text-center small mt-3 mb-0">No account? <a href="<?= url('auth/register.php') ?>">Register</a></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
