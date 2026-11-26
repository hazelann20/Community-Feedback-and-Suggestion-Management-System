<?php require_once __DIR__ . '/functions.php'; $flash = flash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? APP_NAME) ?> | <?= APP_NAME ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark app-nav">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= url(is_admin() ? 'admin/dashboard.php' : (is_logged_in() ? 'user/dashboard.php' : 'index.php')) ?>">
      <i class="bi bi-chat-square-heart"></i> <?= APP_NAME ?>
    </a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if (is_logged_in() && is_admin()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('admin/dashboard.php') ?>">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('admin/feedback.php') ?>">All Feedback</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('admin/categories.php') ?>">Categories</a></li>
        <?php elseif (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('user/dashboard.php') ?>">My Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= url('user/submit.php') ?>">Submit Feedback</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()): ?>
          <li class="nav-item ms-lg-3 text-white-50 small"><?= e($_SESSION['name']) ?> (<?= e($_SESSION['role']) ?>)</li>
          <li class="nav-item ms-lg-3"><a class="btn btn-sm btn-light" href="<?= url('auth/logout.php') ?>">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= url('auth/login.php') ?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-light ms-lg-2" href="<?= url('auth/register.php') ?>">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php if ($flash): ?>
  <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show">
    <?= e($flash['msg']) ?><button class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>
