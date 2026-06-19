<?php
require_once __DIR__ . '/../../config/database.php';

class MouvementRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function logMouvement(int $lotStockId, string $type, int $quantite): bool {
        $stmt = $this->db->prepare("
            INSERT INTO mouvements (lot_stock_id, type, quantite)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$lotStockId, $type, $quantite]);
    }

    public function getAllMouvements(): array {
        return $this->db->query("
            SELECT m.*, l.numero_lot, p.nom as produit_nom
            FROM mouvements m
            JOIN lot_stocks l ON m.lot_stock_id = l.id
            JOIN produits p ON l.produit_id = p.id
            ORDER BY m.date_mouvement DESC
        ")->fetchAll();
    }
}
