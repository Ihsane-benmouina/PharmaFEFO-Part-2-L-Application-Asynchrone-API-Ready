<?php
require_once __DIR__ . '/../../config/database.php';

class ProductRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllProducts(): array {
        return $this->db->query("SELECT * FROM produits ORDER BY nom ASC")->fetchAll();
    }
}
