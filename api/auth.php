<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, X-CSRF-Token');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

$security = new Security();
$db = new Database();
$auth = new Auth();

$action = $_POST['action'] ?? null;

if (!$action) {
    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($body['action'])) {
        $action = $body['action'];
        foreach ($body as $k => $v) {
            if (!isset($_POST[$k])) $_POST[$k] = $v;
        }
    }
}

if (!$action) {
    jsonResponse(['success' => false, 'message' => 'Missing action parameter.'], 400);
}

switch (strtolower($action)) {
    case 'login':
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $csrf = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if ($email === '' || $password === '') {
            jsonResponse(['success' => false, 'message' => 'Email and password are required.'], 400);
        }

        if (!empty($_SESSION['csrf_token']) && $csrf && !$security->validateCsrf($csrf)) {
            jsonResponse(['success' => false, 'message' => 'CSRF validation failed.'], 403);
        }

        $result = $auth->login($email, $password);

        $pdo = $db->getPdo();
        $attempt = $pdo->prepare('INSERT INTO login_attempts (email, ip_address, success) VALUES (:email, :ip, :success)');
        $attempt->execute([
            ':email' => $email,
            ':ip' => $security->getClientIp(),
            ':success' => $result['success'] ? 1 : 0,
        ]);

        if (!$result['success']) {
            jsonResponse(['success' => false, 'message' => $result['message'] ?? 'Invalid credentials.'], 401);
        }

        $user = $result['user'];

        try {
            $rolesStmt = $pdo->prepare(
                'SELECT r.name FROM roles r JOIN user_roles ur ON ur.role_id = r.id WHERE ur.user_id = :uid'
            );
            $rolesStmt->execute([':uid' => $user['id']]);
            $roles = $rolesStmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            $roles = [$user['roles'][0] ?? 'customer'];
        }

        jsonResponse([
            'success' => true,
            'message' => 'Login successful.',
            'user' => [
                'id' => (int) $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'roles' => $roles ?: ['customer'],
            ],
        ]);
        break;

    case 'logout':
        if ($auth->isLoggedIn()) {
            $auth->logout();
            jsonResponse(['success' => true, 'message' => 'Logged out.']);
        }
        jsonResponse(['success' => false, 'message' => 'Not logged in.'], 400);
        break;

    case 'register':
        $allowRegistration = defined('ENABLE_REGISTRATION') ? ENABLE_REGISTRATION : true;
        $role = strtolower(trim($_POST['role'] ?? 'customer'));
        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $currentIsAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

        if (in_array($role, ['admin', 'operations']) && !$currentIsAdmin) {
            jsonResponse(['success' => false, 'message' => 'Insufficient privileges.'], 403);
        }

        if (!$allowRegistration && !$currentIsAdmin) {
            jsonResponse(['success' => false, 'message' => 'Registration is disabled.'], 403);
        }

        if ($email === '' || $password === '' || $fullName === '') {
            jsonResponse(['success' => false, 'message' => 'Full name, email and password are required.'], 400);
        }

        if (strlen($password) < 8) {
            jsonResponse(['success' => false, 'message' => 'Password must be at least 8 characters.'], 400);
        }

        $reg = $auth->register($email, $password, $fullName, $role);

        if (!$reg['success']) {
            jsonResponse(['success' => false, 'message' => $reg['message'] ?? 'Registration failed.'], 400);
        }

        try {
            $uidStmt = $db->getPdo()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $uidStmt->execute([':email' => $email]);
            $uid = $uidStmt->fetchColumn();

            if ($uid) {
                $ridStmt = $db->getPdo()->prepare('SELECT id FROM roles WHERE name = :name LIMIT 1');
                $ridStmt->execute([':name' => $role]);
                $rid = $ridStmt->fetchColumn();

                if ($rid) {
                    $ins = $db->getPdo()->prepare('INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (:uid, :rid)');
                    $ins->execute([':uid' => $uid, ':rid' => $rid]);
                }
            }
        } catch (Exception $e) {
            // ignore
        }

        jsonResponse(['success' => true, 'message' => 'Registration successful. Please login.']);
        break;

    default:
        jsonResponse(['success' => false, 'message' => 'Unknown action.'], 400);
}
