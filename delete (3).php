<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare('DELETE FROM feedback WHERE id = ? AND user_id = ? AND status = "Pending"');
    $st->execute([(int)($_POST['id'] ?? 0), $_SESSION['user_id']]);
    flash($st->rowCount() ? 'Submission deleted.' : 'Only your own Pending submissions can be deleted.',
          $st->rowCount() ? 'success' : 'warning');
}
redirect('user/dashboard.php');
