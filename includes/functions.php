<?php
/**
 * Application helpers.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security.php';

function appUrl($path = '') {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function assetUrl($path) {
    return appUrl('assets/' . ltrim($path, '/'));
}

function currentUser() {
    return $_SESSION['user_name'] ?? null;
}

function currentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function logActivity($action, $description = '', $status = 'success') {
    $db = new Database();
    $userId = $_SESSION['user_id'] ?? null;

    $db->query(
        'INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent, status, created_at) VALUES (:user_id, :action, :description, :ip_address, :user_agent, :status, NOW())',
        [
            ':user_id' => $userId,
            ':action' => $action,
            ':description' => $description,
            ':ip_address' => (new Security())->getClientIp(),
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            ':status' => $status,
        ]
    );
}
