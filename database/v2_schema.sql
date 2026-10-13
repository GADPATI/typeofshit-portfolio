-- typeofshit_portfolio v2 - One-shot upgrade schema
-- Drops and recreates tables safely for upgrade to v2.
-- IMPORTANT: BACKUP your existing database before running this script.
-- This script is designed to be pasted into Hostinger's MySQL SQL query console.

SET FOREIGN_KEY_CHECKS=0;

CREATE DATABASE IF NOT EXISTS `typeofshit_portfolio` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `typeofshit_portfolio`;

-- Optional: archive existing tables (rename) to preserve old data
-- Uncomment to preserve old tables (safe migration path)
-- RENAME TABLE users TO users_old, projects TO projects_old;

-- Roles (normalized for extensibility)
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`name`, `description`) VALUES
('admin', 'Full access'),
('operations', 'Manage projects and view analytics'),
('customer', 'Customer / limited access')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) DEFAULT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User roles join table (many-to-many for flexibility)
DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`, `role_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Projects
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` LONGTEXT DEFAULT NULL,
  `url` VARCHAR(2048) NOT NULL,
  `thumbnail_url` VARCHAR(2048) DEFAULT NULL,
  `category` VARCHAR(100) DEFAULT NULL,
  `tags` JSON DEFAULT NULL,
  `technologies` JSON DEFAULT NULL,
  `year_completed` SMALLINT UNSIGNED DEFAULT NULL,
  `featured` TINYINT(1) DEFAULT 0,
  `is_published` TINYINT(1) DEFAULT 0,
  `view_count` INT UNSIGNED DEFAULT 0,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`created_by`) REFERENCES users(id) ON DELETE SET NULL,
  INDEX (`featured`),
  INDEX (`is_published`),
  INDEX (`category`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity logs
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `action` VARCHAR(150) NOT NULL,
  `entity_type` VARCHAR(100) DEFAULT NULL,
  `entity_id` INT DEFAULT NULL,
  `description` LONGTEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(1024) DEFAULT NULL,
  `status` ENUM('success','failure') DEFAULT 'success',
  `metadata` JSON DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES users(id) ON DELETE SET NULL,
  INDEX (`user_id`),
  INDEX (`action`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions (server-side optional session tracking)
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(128) PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(1024) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (`user_id`),
  INDEX (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Feedback
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `project_id` INT UNSIGNED DEFAULT NULL,
  `rating` TINYINT UNSIGNED DEFAULT NULL,
  `message` LONGTEXT NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `is_approved` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (`project_id`) REFERENCES projects(id) ON DELETE CASCADE,
  INDEX (`user_id`),
  INDEX (`project_id`),
  INDEX (`is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(150) NOT NULL UNIQUE,
  `setting_value` LONGTEXT DEFAULT NULL,
  `is_public` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login attempts for rate limiting
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `success` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (`email`),
  INDEX (`ip_address`),
  INDEX (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrations table (track schema upgrades)
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  `run_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Setup tokens: used to perform secure first-run admin password set via a short-time token
DROP TABLE IF EXISTS `setup_tokens`;
CREATE TABLE `setup_tokens` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `token` VARCHAR(128) NOT NULL UNIQUE,
  `purpose` VARCHAR(100) NOT NULL,
  `data` JSON DEFAULT NULL,
  `expires_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial settings
INSERT INTO settings (setting_key, setting_value, is_public)
VALUES
('site_title', 'TypeOfShit Portfolio'),
('site_description', 'Showcase your premium work'),
('newsletter_enabled', '1'),
('featured_projects_count', '3'),
('max_projects_per_user', '100')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- Create a placeholder admin user with no password set yet (secure first-run flow)
-- After running this SQL, use the provided setup_admin.php in the repo to set the admin password using the setup token below.

INSERT INTO users (email, password_hash, full_name, is_active, email_verified)
VALUES ('admin@typeofshit.com', NULL, 'Admin User', 1, 1)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), is_active = VALUES(is_active);

-- Map admin user to admin role
INSERT IGNORE INTO user_roles (user_id, role_id)
SELECT u.id, r.id FROM users u CROSS JOIN roles r WHERE u.email = 'admin@typeofshit.com' AND r.name = 'admin';

-- Insert a single-use setup token valid for 2 hours to be used by setup_admin.php (paste token into the URL once)
-- NOTE: change token_prefix to a secret of your choosing if you don't want to use this auto-generated token
DROP PROCEDURE IF EXISTS _insert_setup_token;
DELIMITER $$
CREATE PROCEDURE _insert_setup_token()
BEGIN
  DECLARE tok VARCHAR(128);
  SET tok = CONCAT('setup_', LEFT(HEX(RAND_BYTES(16)), 24));
  INSERT INTO setup_tokens (token, purpose, data, expires_at) VALUES (tok, 'initial_admin_password', JSON_OBJECT('email','admin@typeofshit.com'), DATE_ADD(NOW(), INTERVAL 2 HOUR));
  SELECT tok AS setup_token;
END$$
DELIMITER ;

-- Run the procedure to insert and return the token (Hostinger's SQL UI may or may not show the result row)
CALL _insert_setup_token();
DROP PROCEDURE IF EXISTS _insert_setup_token;

SET FOREIGN_KEY_CHECKS=1;

-- v2 schema creation complete

-- Instructions (also provided in repo):
-- 1) If your Hostinger SQL console returns the setup_token value from CALL, copy it. Otherwise, run:
--    SELECT token, expires_at FROM setup_tokens WHERE purpose='initial_admin_password' ORDER BY created_at DESC LIMIT 1;
-- 2) Upload setup_admin.php to your webroot (provided in repo) and visit:
--    https://your-site.example/setup_admin.php?token=<the_token>
-- 3) Use the form to set a secure admin password. The script will hash the password with bcrypt and store it.
-- 4) Remove setup_admin.php after completing setup.

-- End of v2 schema
