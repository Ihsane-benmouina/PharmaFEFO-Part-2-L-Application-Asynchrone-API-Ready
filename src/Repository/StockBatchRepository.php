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

    public function getAllProducts(): array {
        return $this->db->query("SELECT * FROM produits ORDER BY nom ASC")->fetchAll();
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

    public function getDashboardLots(?string $filter = null): array {
        $sql = "SELECT l.*, p.nom as produit_nom, p.reference
                FROM lot_stocks l
                JOIN produits p ON l.produit_id = p.id
                WHERE l.quantite > 0 AND l.statut != 'EXPIRED'";
        if ($filter === 'ROUGE') {
            $sql .= " AND l.date_peremption < DATE_ADD(CURDATE(), INTERVAL 30 DAY)";
        }
        $sql .= " ORDER BY l.date_peremption ASC";
        return $this->db->query($sql)->fetchAll();
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

    public function getFefoBatchForProduct(int $productId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM lot_stocks
            WHERE produit_id = ? AND quantite > 0 AND statut != 'EXPIRED'
            ORDER BY date_peremption ASC
            LIMIT 1
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function dispenseBatch(int $batchId, int $quantity): bool {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("UPDATE lot_stocks SET quantite = quantite - ? WHERE id = ?");
            $stmt->execute([$quantity, $batchId]);
            $this->mouvementRepo->logMouvement($batchId, 'SORTIE', $quantity);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function markBatchAsExpired(int $batchId): bool {
        $stmt = $this->db->prepare("UPDATE lot_stocks SET statut = 'EXPIRED', quantite = 0 WHERE id = ?");
        return $stmt->execute([$batchId]);
    }

    public function getFinancialLossTotal(): float {
        $sql = "SELECT SUM(l.quantite * p.prix) as total
                FROM lot_stocks l
                JOIN produits p ON l.produit_id = p.id
                WHERE l.statut = 'EXPIRED'";
        $res = $this->db->query($sql)->fetch();
        return $res['total'] ? (float)$res['total'] : 0.0;
    }
}
