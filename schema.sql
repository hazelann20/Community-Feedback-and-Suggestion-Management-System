-- Community Feedback and Suggestion Management System
-- Import this file in phpMyAdmin (or: mysql -u root < schema.sql)

CREATE DATABASE IF NOT EXISTS community_feedback
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE community_feedback;

DROP TABLE IF EXISTS feedback;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  category_id INT NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium',
  status ENUM('Pending','In Review','Resolved','Closed') NOT NULL DEFAULT 'Pending',
  admin_response TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB;

INSERT INTO categories (name) VALUES
 ('Infrastructure'), ('Environment'), ('Services'), ('Safety'), ('Other');

-- Default admin account: admin@community.local / admin123  (change after first login)
INSERT INTO users (full_name, email, password, role) VALUES
 ('System Administrator', 'admin@community.local',
  '$2y$10$BkUHll8Wc27m04E7nMEu1u9JJtAkcAPZiWTqmCQy1UODAUYMnZm1m', 'admin');
