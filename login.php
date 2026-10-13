<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/security.php';

$security = new Security();
$auth = new Auth();
$csrf = $security->csrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$security->validateCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Security validation failed.';
    } else {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $result = $auth->login($email, $password);
        if ($result['success']) {
            $role = $_SESSION['user_role'] ?? 'customer';
            if ($role === 'admin') {
                header('Location: /admin/index.php');
            } elseif ($role === 'operations') {
                header('Location: /operations/index.php');
            } else {
                header('Location: /customer/index.php');
            }
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — typeofshit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
</head>
<body class="auth-page">
    <div class="noise-overlay"></div>
    <div class="auth-shell reveal">
        <div class="auth-panel">
            <div class="auth-brand">
                <span class="brand-mark">-</span>
                <span class="brand-name">typeofshit</span>
            </div>
            <h1>login</h1>

            <?php if (!empty($error)): ?>
                <div class="alert error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <label>
                    <span>Email</span>
                    <input type="email" name="email" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" required>
                </label>
                <button type="submit">sign in</button>
            </form>
            <p class="auth-foot">Need access? Contact the admin.</p>
        </div>
    </div>
</body>
</html>
