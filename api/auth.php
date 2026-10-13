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

RBAC::requireRole('customer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Panel — typeofshit</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
</head>
<body class="dashboard-page customer-page">
    <div class="noise-overlay"></div>
    <header class="dashboard-header">
        <div class="brand-wrap">
            <span class="brand-mark">-</span>
            <span class="brand-name">typeofshit</span>
        </div>
        <div class="dashboard-actions">
            <span class="role-pill">customer</span>
            <a href="/logout.php" class="cart-button">logout</a>
        </div>
    </header>

    <main class="dashboard-shell">
        <aside class="sidebar">
            <a class="active" href="#">home</a>
            <a href="#">projects</a>
            <a href="#">feedback</a>
        </aside>
        <section class="content-panel">
            <h1>Customer dashboard</h1>
            <div class="stats-grid">
                <div class="stat-box"><span>Portfolio</span><strong>12</strong></div>
                <div class="stat-box"><span>Feedback</span><strong>3</strong></div>
                <div class="stat-box"><span>Favorites</span><strong>8</strong></div>
            </div>
        </section>
    </main>
</body>
</html>
