<?php
// Global settings. Adjust BASE_URL to match the folder name in htdocs.
define('APP_NAME', 'Community Feedback Portal');
define('BASE_URL', '/feedback-system');

define('DB_HOST', 'localhost');
define('DB_NAME', 'community_feedback');
define('DB_USER', 'root');
define('DB_PASS', '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
