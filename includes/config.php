<?php
/**
 * Application configuration.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'typeofshit_portfolio');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'typeofshit');
define('APP_URL', 'http://localhost:8000');
define('APP_ENV', 'development');
define('APP_DEBUG', true);

define('SECRET_KEY', 'replace_this_with_a_long_random_secret');
define('SESSION_LIFETIME', 1800);
define('BCRYPT_COST', 12);

define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('LOG_PATH', __DIR__ . '/../logs/');
