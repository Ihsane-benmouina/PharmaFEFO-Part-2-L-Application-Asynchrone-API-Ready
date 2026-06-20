<?php
// src/Controller/Web/AuthController.php

require_once __DIR__ . '/../../Service/AuthService.php';

class AuthController
{
    public function __construct() {}

    /**
     * GET/POST index.php?action=login
     * Affiche le template login.php (squelette pur).
     * La soumission est interceptée par login.js via POST /api/v1/login.
     */
    public function login(): void
    {
        AuthService::initSession();

        // Si déjà connecté, rediriger vers le dashboard
        if (AuthService::isAuthenticated()) {
            header("Location: index.php?action=dashboard");
            exit;
        }

        // Afficher simplement le template HTML statique
        include __DIR__ . '/../../../templates/auth/login.php';
    }

    /**
     * GET index.php?action=logout
     */
    public function logout(): void
    {
        AuthService::logout();
        header("Location: index.php?action=login");
        exit;
    }
}
