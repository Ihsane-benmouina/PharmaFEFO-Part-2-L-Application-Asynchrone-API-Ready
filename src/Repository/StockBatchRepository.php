<?php
// src/Repository/StockBatchRepository.php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/MouvementRepository.php';

class StockBatchRepository {
    private PDO $db;
    private MouvementRepository $mouvementRepo;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->mouvementRepo = new MouvementRepository();
    }
    public function saveInputBatch(int $productId, string $lotNumber, int $quantity, string $expiryDate): bool {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("
                INSERT INTO lot_stocks (produit_id, numero_lot, quantite, date_peremption, statut)
                VALUES (?, ?, ?, ?, 'OK')
            ");
            $stmt->execute([$productId, $lotNumber, $quantity, $expiryDate]);
            $lotId = $this->db->lastInsertId();
            $this->mouvementRepo->logMouvement((int)$lotId, 'ENTREE', $quantity);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}