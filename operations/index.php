<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/rbac.php';

$auth = new Auth();
if (!$auth->isLoggedIn()) {
    header('Location: /login.php');
    exit;
}

RBAC::requireRole('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — typeofshit</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
</head>
<body class="dashboard-page admin-page">
    <div class="noise-overlay"></div>
    <header class="dashboard-header">
        <div class="brand-wrap">
            <span class="brand-mark">-</span>
            <span class="brand-name">typeofshit</span>
        </div>
        <div class="dashboard-actions">
            <span class="role-pill">admin</span>
            <a href="/logout.php" class="cart-button">logout</a>
        </div>
    </header>

    <main class="dashboard-shell">
        <aside class="sidebar admin-sidebar">
            <a class="active" href="#">overview</a>
            <a href="#">users</a>
            <a href="#">projects</a>
            <a href="#">analytics</a>
            <a href="#">settings</a>
        </aside>
        <section class="content-panel">
            <h1>Admin control room</h1>
            <div class="stats-grid">
                <div class="stat-box"><span>users</span><strong>14</strong></div>
                <div class="stat-box"><span>projects</span><strong>42</strong></div>
                <div class="stat-box"><span>log events</span><strong>1,328</strong></div>
            </div>
            <div class="analytics-table">
                <table>
                    <thead>
                        <tr><th>Action</th><th>User</th><th>Status</th><th>Time</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>login</td><td>admin@typeofshit.com</td><td>success</td><td>now</td></tr>
                        <tr><td>project update</td><td>ops@typeofshit.com</td><td>success</td><td>15m</td></tr>
                        <tr><td>portfolio review</td><td>customer</td><td>success</td><td>1h</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
