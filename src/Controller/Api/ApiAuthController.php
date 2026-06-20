<?php
// src/Controller/Api/ApiAuthController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Repository/UserRepository.php';

class ApiAuthController
{
    public function __construct()
    {
        header('Content-Type: application/json');
    }

    /**
     * POST /api/v1/login
     * Authentifie l'utilisateur et initialise la session.
     */
    public function login(): void
    {
        AuthService::initSession();

        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $email    = trim($body['email']    ?? $_POST['email']    ?? '');
        $password =      $body['password'] ?? $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            http_response_code(422);
            echo json_encode(["success" => false, "message" => "Email et mot de passe requis."]);
            return;
        }

        $repo = new UserRepository();
        $user = $repo->findUserByEmail($email);

        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nom']  = $user['nom'] . ' ' . $user['prenom'];
            $_SESSION['user_role'] = $user['role'];

            echo json_encode([
                "success" => true,
                "message" => "Connexion réussie.",
                "data" => [
                    "nom"  => $_SESSION['user_nom'],
                    "role" => $_SESSION['user_role'],
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Identifiants invalides."]);
        }
    }

    /**
     * GET /api/v1/me
     * Retourne les informations de la session courante.
     */
    public function me(): void
    {
        AuthService::initSession();

        if (!AuthService::isAuthenticated()) {
            http_response_code(401);
            echo json_encode(["success" => false, "message" => "Non authentifié."]);
            return;
        }

        echo json_encode([
            "success" => true,
            "message" => "Session active.",
            "data" => [
                "nom"  => $_SESSION['user_nom']  ?? '',
                "role" => $_SESSION['user_role'] ?? '',
            ]
        ]);
    }
}
