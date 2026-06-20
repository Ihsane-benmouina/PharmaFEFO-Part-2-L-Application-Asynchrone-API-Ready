<?php
require_once __DIR__ . '/../../config/database.php';

class UserRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function findUserByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function getAllUsers(): array {
        return $this->db->query("SELECT id, nom, prenom, email, role FROM users ORDER BY id DESC")->fetchAll();
    }

    public function createUser(string $nom, string $prenom, string $email, string $hashedPassword, string $role): bool {
        $stmt = $this->db->prepare("
            INSERT INTO users (nom, prenom, email, password, role)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$nom, $prenom, $email, $hashedPassword, $role]);
    }
}
