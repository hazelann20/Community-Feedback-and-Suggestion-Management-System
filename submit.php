<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

$cats = get_categories();
$errors = [];
$data = ['title' => '', 'category_id' => 0, 'priority' => 'Medium', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    [$errors, $data] = validate_feedback($_POST);
    if (!$errors) {
        db()->prepare('INSERT INTO feedback (user_id, category_id, title, description, priority, status)
                       VALUES (?, ?, ?, ?, ?, "Pending")')
            ->execute([$_SESSION['user_id'], $data['category_id'], $data['title'], $data['description'], $data['priority']]);
        flash('Your feedback was submitted and is now Pending review.');
        redirect('user/dashboard.php');
    }
}

$pageTitle = 'Submit Feedback';
$buttonLabel = 'Submit';
include __DIR__ . '/../includes/header.php';
?>
<div class="card p-4 mx-auto" style="max-width:720px">
  <h4 class="mb-3">Submit Feedback or Suggestion</h4>
  <?php include __DIR__ . '/_form.php'; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
