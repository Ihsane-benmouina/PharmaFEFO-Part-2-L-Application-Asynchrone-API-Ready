<?php
// src/Service/AuthService.php

class AuthService
{
    public static function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isAuthenticated(): bool
    {
        self::initSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    public static function hasRole(string $role): bool
    {
        if (!self::isAuthenticated()) return false;
        return strtolower($_SESSION['user_role']) === strtolower($role);
    }

    /**
     * @param bool $isApi  true = réponse JSON 403 | false = HTML 403
     */
    public static function requireRole(string $role, bool $isApi = true): void
    {
        if (!self::hasRole($role)) {
            if ($isApi) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode([
                    "success" => false,
                    "message" => "Accès refusé : Privilèges [ " . strtoupper($role) . " ] requis."
                ]);
            } else {
                http_response_code(403);
                echo "<div style='padding:20px;color:#991b1b;background:#fef2f2;font-family:sans-serif'><strong>HTTP 403 :</strong> Droits insuffisants.</div>";
            }
            exit;
        }
    }

    public static function requireAnyRole(array $roles, bool $isApi = true): void
    {
        foreach ($roles as $role) {
            if (self::hasRole($role)) return;
        }

        if ($isApi) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(["success" => false, "message" => "Accès refusé : Droits insuffisants."]);
        } else {
            http_response_code(403);
            echo "<div style='padding:20px;color:#991b1b;background:#fef2f2;font-family:sans-serif'><strong>HTTP 403 :</strong> Privilèges insuffisants.</div>";
        }
        exit;
    }

    public static function logout(): void
    {
        self::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }
}
