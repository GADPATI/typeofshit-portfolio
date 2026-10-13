<?php
/**
 * Authentication helper.
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/security.php';

class Auth {
    private $db;
    private $security;

    public function __construct() {
        $this->db = new Database();
        $this->security = new Security();
    }

    public function login($email, $password) {
        $stmt = $this->db->query('SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1', [
            ':email' => strtolower(trim($email)),
        ]);

        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];

        $this->db->query('UPDATE users SET last_login = NOW() WHERE id = :id', [
            ':id' => $user['id'],
        ]);

        return ['success' => true, 'message' => 'Login successful.', 'user' => $user];
    }

    public function logout() {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    public function isLoggedIn() {
        return !empty($_SESSION['user_id']);
    }

    public function currentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        $stmt = $this->db->query('SELECT * FROM users WHERE id = :id LIMIT 1', [
            ':id' => $_SESSION['user_id'],
        ]);
        return $stmt->fetch();
    }

    public function register($fullName, $email, $password, $role = 'customer') {
        $fullName = trim($fullName);
        $email = strtolower(trim($email));

        if ($fullName === '' || $email === '') {
            return ['success' => false, 'message' => 'Name and email are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format.'];
        }

        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters.'];
        }

        $check = $this->db->query('SELECT id FROM users WHERE email = :email LIMIT 1', [':email' => $email]);
        if ($check->fetch()) {
            return ['success' => false, 'message' => 'This email is already registered.'];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);

        $this->db->query(
            'INSERT INTO users (full_name, email, password_hash, role, is_active, email_verified, created_at) VALUES (:full_name, :email, :password_hash, :role, 1, 1, NOW())',
            [
                ':full_name' => $fullName,
                ':email' => $email,
                ':password_hash' => $hash,
                ':role' => $role,
            ]
        );

        return ['success' => true, 'message' => 'Registration successful.'];
    }
}
