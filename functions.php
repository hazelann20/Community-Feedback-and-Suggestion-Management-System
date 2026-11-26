<?php
require_once __DIR__ . '/../config/database.php';

function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return BASE_URL . '/' . ltrim($path, '/'); }

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function is_logged_in(): bool { return isset($_SESSION['user_id']); }
function is_admin(): bool { return ($_SESSION['role'] ?? '') === 'admin'; }

function require_login(): void {
    if (!is_logged_in()) redirect('auth/login.php');
}
function require_admin(): void {
    require_login();
    if (!is_admin()) redirect('user/dashboard.php');
}

function flash(?string $msg = null, string $type = 'success') {
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Invalid request token.');
    }
}

function status_badge(string $s): string {
    $map = ['Pending' => 'warning text-dark', 'In Review' => 'info text-dark',
            'Resolved' => 'success', 'Closed' => 'secondary'];
    return '<span class="badge bg-' . ($map[$s] ?? 'secondary') . '">' . e($s) . '</span>';
}
function priority_badge(string $p): string {
    $map = ['Low' => 'success', 'Medium' => 'primary', 'High' => 'danger'];
    return '<span class="badge bg-' . ($map[$p] ?? 'secondary') . '">' . e($p) . '</span>';
}

function get_categories(): array {
    return db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
}

const STATUSES = ['Pending', 'In Review', 'Resolved', 'Closed'];
const PRIORITIES = ['Low', 'Medium', 'High'];

/** Validate a feedback form. Returns [errors, cleanData]. */
function validate_feedback(array $in): array {
    $errors = [];
    $d = [
        'title' => trim($in['title'] ?? ''),
        'category_id' => (int)($in['category_id'] ?? 0),
        'priority' => $in['priority'] ?? '',
        'description' => trim($in['description'] ?? ''),
    ];
    if ($d['title'] === '' || mb_strlen($d['title']) > 150) $errors[] = 'Title is required (max 150 characters).';
    if (!in_array($d['priority'], PRIORITIES, true)) $errors[] = 'Select a valid priority.';
    if (mb_strlen($d['description']) < 10) $errors[] = 'Description must be at least 10 characters.';
    $st = db()->prepare('SELECT COUNT(*) FROM categories WHERE id = ?');
    $st->execute([$d['category_id']]);
    if (!$st->fetchColumn()) $errors[] = 'Select a valid category.';
    return [$errors, $d];
}
