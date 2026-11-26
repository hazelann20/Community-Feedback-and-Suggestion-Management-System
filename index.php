<?php
require_once __DIR__ . '/includes/functions.php';
if (is_logged_in()) redirect(is_admin() ? 'admin/dashboard.php' : 'user/dashboard.php');
$pageTitle = 'Welcome';
include __DIR__ . '/includes/header.php';
?>
<div class="hero text-center mb-4">
  <h1 class="display-5 fw-bold">Your voice shapes our community</h1>
  <p class="lead mb-4">Submit feedback, suggestions and concerns, then track every step until it is resolved.</p>
  <a href="<?= url('auth/register.php') ?>" class="btn btn-light btn-lg me-2">Get Started</a>
  <a href="<?= url('auth/login.php') ?>" class="btn btn-outline-light btn-lg">Login</a>
</div>
<div class="row g-3 text-center">
  <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-pencil-square fs-1 text-primary"></i><h5 class="mt-2">Submit</h5><p class="text-muted mb-0">Share an idea or report an issue in a minute.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-clipboard-check fs-1 text-primary"></i><h5 class="mt-2">Reviewed</h5><p class="text-muted mb-0">Administrators evaluate and update the status.</p></div></div>
  <div class="col-md-4"><div class="card p-4 h-100"><i class="bi bi-graph-up fs-1 text-primary"></i><h5 class="mt-2">Track</h5><p class="text-muted mb-0">See responses and progress until closure.</p></div></div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
