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
     public function getNextMonthAlerts(): array {
        $sql = "SELECT l.*, p.nom as produit_nom
                FROM lot_stocks l
                JOIN produits p ON l.produit_id = p.id
                WHERE l.date_peremption BETWEEN DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
                                             AND DATE_ADD(CURDATE(), INTERVAL 2 MONTH)
                AND l.quantite > 0 AND l.statut != 'EXPIRED'";
        return $this->db->query($sql)->fetchAll();
    }
}