<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$security = new Security();
$auth = new Auth();

$action = $_POST['action'] ?? '';
if ($action === 'login') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    if (!$security->validateCsrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'CSRF validation failed.']);
        exit;
    }

    $result = $auth->login($email, $password);
    echo json_encode($result);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
