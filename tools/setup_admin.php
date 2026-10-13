<?php
// tools/setup_admin.php
// Secure one-time admin password setup.
// Usage: upload file to webroot, visit with token ?token=... then set a strong password.

session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';

$db = new Database();

$token = $_GET['token'] ?? null;
if (!$token) {
    http_response_code(400);
    echo "Token required. Provide ?token=...";
    exit;
}

// fetch token
$stmt = $db->getPdo()->prepare('SELECT * FROM setup_tokens WHERE token = :token AND purpose = :purpose AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1');
$stmt->execute([':token' => $token, ':purpose' => 'initial_admin_password']);
$st = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$st) {
    http_response_code(404);
    echo "Invalid or expired token.";
    exit;
}

// get email from data
$tokenData = json_decode($st['data'] ?? '{}', true);
$adminEmail = $tokenData['email'] ?? 'admin@typeofshit.com';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    if (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $pdo = $db->getPdo();
        // update user
        $u = $pdo->prepare('UPDATE users SET password_hash = :hash, email_verified = 1 WHERE email = :email');
        $u->execute([':hash' => $hash, ':email' => $adminEmail]);
        // delete token
        $d = $pdo->prepare('DELETE FROM setup_tokens WHERE id = :id');
        $d->execute([':id' => $st['id']]);

        echo "Admin password set successfully. Remove this file now.";
        exit;
    }
}

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Setup Admin Password</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Inter,Arial;margin:2rem;background:#f6f5f2}
    .card{max-width:560px;margin:2rem auto;padding:2rem;border-radius:1rem;background:#fff}
    label{display:block;margin-bottom:.5rem}
    input{width:100%;padding:.8rem;margin-bottom:1rem;border:1px solid #ddd;border-radius:.6rem}
    button{background:#01472e;color:#fff;border:none;padding:.8rem 1rem;border-radius:.6rem}
    .error{color:#b00020;margin-bottom:1rem}
  </style>
</head>
<body>
  <div class="card">
    <h2>Initial Admin Setup</h2>
    <p>Setting password for <strong><?= htmlspecialchars($adminEmail) ?></strong></p>
    <?php if (!empty($error)): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <label>New password (min 8 chars)</label>
      <input name="password" type="password" required>
      <label>Confirm password</label>
      <input name="password_confirm" type="password" required>
      <button type="submit">Set password</button>
    </form>
  </div>
</body>
</html>
