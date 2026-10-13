<?php
/**
 * Role based access control.
 */
class RBAC {
    public static function requireRole($role) {
        if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            http_response_code(403);
            header('Location: /login.php');
            exit;
        }
    }

    public static function requireAnyRole(array $roles) {
        if (empty($_SESSION['user_role']) || !in_array($_SESSION['user_role'], $roles, true)) {
            http_response_code(403);
            header('Location: /login.php');
            exit;
        }
    }
}
