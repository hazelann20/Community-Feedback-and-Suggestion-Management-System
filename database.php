<?php
require_once __DIR__ . '/config.php';

function ensure_schema(PDO $pdo): void {
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(120) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM("user","admin") NOT NULL DEFAULT "user",
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB');

    $pdo->exec('CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(60) NOT NULL UNIQUE
    ) ENGINE=InnoDB');

    $pdo->exec('CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        category_id INT NOT NULL,
        title VARCHAR(150) NOT NULL,
        description TEXT NOT NULL,
        priority ENUM("Low","Medium","High") NOT NULL DEFAULT "Medium",
        status ENUM("Pending","In Review","Resolved","Closed") NOT NULL DEFAULT "Pending",
        admin_response TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    ) ENGINE=InnoDB');

    $pdo->exec("INSERT IGNORE INTO categories (name) VALUES
        ('Infrastructure'), ('Environment'), ('Services'), ('Safety'), ('Other')");

    $adminPasswordHash = '$2y$10$BkUHll8Wc27m04E7nMEu1u9JJtAkcAPZiWTqmCQy1UODAUYMnZm1m';
    $pdo->exec("INSERT IGNORE INTO users (full_name, email, password, role) VALUES
        ('System Administrator', 'admin@community.local', '$adminPasswordHash', 'admin')");
}

function db(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    try {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, $options);
        $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', DB_NAME) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, $options);
        ensure_schema($pdo);
    } catch (PDOException $e) {
        die('Database connection failed. Check config/config.php and verify MySQL is running.');
    }

    return $pdo;
}
