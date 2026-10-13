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

RBAC::requireAnyRole(['admin', 'operations']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operations Panel — typeofshit</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
</head>
<body class="dashboard-page operations-page">
    <div class="noise-overlay"></div>
    <header class="dashboard-header">
        <div class="brand-wrap">
            <span class="brand-mark">-</span>
            <span class="brand-name">typeofshit</span>
        </div>
        <div class="dashboard-actions">
            <span class="role-pill">operations</span>
            <a href="/logout.php" class="cart-button">logout</a>
        </div>
    </header>

    <main class="dashboard-shell">
        <aside class="sidebar">
            <a class="active" href="#">portfolio</a>
            <a href="#">projects</a>
            <a href="#">shipments</a>
            <a href="#">logs</a>
        </aside>
        <section class="content-panel">
            <h1>Operations overview</h1>
            <div class="stats-grid">
                <div class="stat-box"><span>Active URLs</span><strong>21</strong></div>
                <div class="stat-box"><span>Pending edits</span><strong>5</strong></div>
                <div class="stat-box"><span>Reviews</span><strong>18</strong></div>
            </div>
        </section>
    </main>
</body>
</html>
