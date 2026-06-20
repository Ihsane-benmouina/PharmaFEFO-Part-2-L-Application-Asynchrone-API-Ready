<?php
// src/Controller/Api/ApiUsersController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Repository/UserRepository.php';

class ApiUsersController
{
    private UserRepository $userRepo;

    public function __construct()
    {
        header('Content-Type: application/json');
        $this->userRepo = new UserRepository();
    }

    /**
     * GET /api/v1/users
     * Retourne la liste de tous les utilisateurs (admin uniquement).
     */
    public function listUsers(): void
    {
        AuthService::requireRole('admin');

        $users = $this->userRepo->getAllUsers();

        echo json_encode([
            "success" => true,
            "message" => "Opération réussie.",
            "data"    => $users
        ]);
    }

    /**
     * POST /api/v1/users
     * Crée un nouvel utilisateur (admin uniquement).
     */
    public function createUser(): void
    {
        AuthService::requireRole('admin');

        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $nom     = trim($body['nom']      ?? $_POST['nom']      ?? '');
        $prenom  = trim($body['prenom']   ?? $_POST['prenom']   ?? '');
        $email   = trim($body['email']    ?? $_POST['email']    ?? '');
        $password =     $body['password'] ?? $_POST['password'] ?? '';
        $role    = trim($body['role']     ?? $_POST['role']     ?? 'preparateur');

        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            http_response_code(422);
            echo json_encode(["success" => false, "message" => "Tous les champs sont obligatoires."]);
            return;
        }

        $validRoles = ['pharmacien', 'preparateur'];
        if (!in_array($role, $validRoles)) {
            http_response_code(422);
            echo json_encode(["success" => false, "message" => "Rôle invalide. Valeurs autorisées : pharmacien, preparateur."]);
            return;
        }

        $hashed  = password_hash($password, PASSWORD_BCRYPT);
        $created = $this->userRepo->createUser($nom, $prenom, $email, $hashed, $role);

        if ($created) {
            echo json_encode([
                "success" => true,
                "message" => "Utilisateur créé avec succès.",
                "data"    => null
            ]);
        } else {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Cet e-mail est déjà utilisé par un autre compte."
            ]);
        }
    }
}
