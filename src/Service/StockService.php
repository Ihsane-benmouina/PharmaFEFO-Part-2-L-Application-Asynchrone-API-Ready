<?php
// src/Service/StockService.php

require_once __DIR__ . '/../Repository/StockBatchRepository.php';
require_once __DIR__ . '/../Repository/ProductRepository.php';

class StockService
{
    private StockBatchRepository $batchRepo;
    private ProductRepository    $productRepo;

    public function __construct()
    {
        $this->batchRepo   = new StockBatchRepository();
        $this->productRepo = new ProductRepository();
    }

    /**
     * Prépare les lots du dashboard avec badge et couleur calculés côté PHP.
     * Utilisé par le Web/DashboardController ET par l'API (GET /api/v1/batches).
     *
     * @param string|null $filter  null|'ROUGE'|'critical'
     */
    public function processBatchesForDashboard(?string $filter = null): array
    {
        // CORRECTION : mapper le filtre JS 'critical' vers le filtre SQL 'ROUGE'
        $sqlFilter = null;
        if ($filter === 'ROUGE' || $filter === 'critical') {
            $sqlFilter = 'ROUGE';
        }

        $rawLots       = $this->batchRepo->getDashboardLots($sqlFilter);
        $processedLots = [];
        $today         = new DateTime();

        foreach ($rawLots as $l) {
            $expiryStr = $l['date_peremption'] ?? '';
            $status    = $l['statut'] ?? 'OK';

            if ($status === 'EXPIRED') {
                $l['color_classes'] = 'bg-rose-100 text-rose-900 border-rose-300';
                $l['badge_text']    = 'EXPIRED (Périmé)';
            } else {
                $dlu   = new DateTime($expiryStr);
                $diff  = $today->diff($dlu);
                $jours = $diff->days * ($diff->invert ? -1 : 1);

                if ($jours <= 0) {
                    $l['color_classes'] = 'bg-rose-100 text-rose-900 border-rose-300';
                    $l['badge_text']    = 'EXPIRED (Périmé)';
                } elseif ($jours < 30) {
                    $l['color_classes'] = 'bg-red-50 text-red-900 border-red-200';
                    $l['badge_text']    = 'Rouge (< 30 jours)';
                } elseif ($jours < 90) {
                    $l['color_classes'] = 'bg-orange-50 text-orange-900 border-orange-200';
                    $l['badge_text']    = 'Orange (< 90 jours)';
                } else {
                    $l['color_classes'] = 'bg-green-50 text-green-900 border-green-200';
                    $l['badge_text']    = 'Vert (> 6 mois)';
                }
            }
            $processedLots[] = $l;
        }
        return $processedLots;
    }

    /**
     * Sérialise les lots pour la réponse JSON de l'API.
     * Les clés sont en camelCase pour correspondre au JS existant.
     */
    public function processBatchesForApi(?string $filter = null): array
    {
        $lots   = $this->processBatchesForDashboard($filter);
        $result = [];

        foreach ($lots as $l) {
            $result[] = [
                'id'          => (int)$l['id'],
                'productId'   => (int)$l['produit_id'],
                'productName' => $l['produit_nom']    ?? '',
                'reference'   => $l['reference']      ?? '',
                'batchNumber' => $l['numero_lot']     ?? '',
                'quantity'    => (int)$l['quantite'],
                'expiryDate'  => $l['date_peremption'] ?? '',
                'status'      => $l['statut']          ?? 'OK',
                'color_classes' => $l['color_classes'],
                'badge_text'    => $l['badge_text'],
            ];
        }
        return $result;
    }

    public function getNextMonthAlertsCount(): int
    {
        $alerts = $this->batchRepo->getNextMonthAlerts();
        return is_array($alerts) ? count($alerts) : 0;
    }

    public function getAllProducts(): array
    {
        return $this->productRepo->getAllProducts();
    }

    /**
     * CORRECTION BUG CRITIQUE : 'Y-m-getFefoBatchForProductH:i:s' → 'Y-m-d'
     */
    public function createBatch(int $productId, string $lotNumber, int $quantity, string $expiryDate): bool
    {
        $today     = new DateTime((new DateTime())->format('Y-m-d')); // BUG CORRIGÉ
        $inputDate = new DateTime($expiryDate);

        if ($inputDate < $today) {
            return false;
        }

        return $this->batchRepo->saveInputBatch($productId, $lotNumber, $quantity, $expiryDate);
    }

    public function dispenseFefo(int $productId, int $quantity): bool
    {
        if ($quantity <= 0) return false;

        $suggestedBatch = $this->batchRepo->getFefoBatchForProduct($productId);

        if (!$suggestedBatch || (int)$suggestedBatch['quantite'] < $quantity) {
            return false;
        }

        return $this->batchRepo->dispenseBatch((int)$suggestedBatch['id'], $quantity);
    }

    public function markBatchAsExpired(int $batchId): bool
    {
        return $this->batchRepo->markBatchAsExpired($batchId);
    }

    public function getFinancialLossReportTotal(): float
    {
        return (float)$this->batchRepo->getFinancialLossTotal();
    }
}
