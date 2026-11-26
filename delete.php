<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    db()->prepare('DELETE FROM feedback WHERE id = ?')->execute([(int)($_POST['id'] ?? 0)]);
    flash('Feedback deleted.');
}
redirect('admin/feedback.php');
