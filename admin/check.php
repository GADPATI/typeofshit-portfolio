<?php
session_start();
$hasAccess = $_SESSION['user_role'] ?? null;
if (!$hasAccess) {
    header('Location: /login.php');
    exit;
}

if ($_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}
?>
