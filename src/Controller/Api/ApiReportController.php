<?php
// src/Controller/Api/ApiReportController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Service/StockService.php';

class ApiReportController
{
    private StockService $stockService;

    public function __construct()
    {
        header('Content-Type: application/json');
        $this->stockService = new StockService();
    }

    /**
     * GET /api/v1/report/loss
     * Retourne le total financier des pertes (lots EXPIRED).
     * Réservé à l'admin.
     */
    public function financialLoss(): void
    {
        AuthService::requireRole('admin');

        $total = $this->stockService->getFinancialLossReportTotal();

        echo json_encode([
            "success" => true,
            "message" => "Opération réussie.",
            "data"    => [
                "totalLoss"          => $total,
                "totalLossFormatted" => number_format($total, 2)
            ]
        ]);
    }

    /**
     * GET /api/v1/dashboard/notifications
     * Retourne les lots expirant le mois prochain (pour la page notifications).
     */
    public function notifications(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        require_once __DIR__ . '/../../Repository/StockBatchRepository.php';
        $repo  = new StockBatchRepository();
        $items = $repo->getNextMonthAlerts();

        $data = array_map(fn($n) => [
            'id'             => (int)$n['id'],
            'produitNom'     => $n['produit_nom']     ?? '',
            'numeroLot'      => $n['numero_lot']      ?? '',
            'datePeremption' => $n['date_peremption'] ?? '',
            'quantite'       => (int)$n['quantite'],
        ], $items);

        echo json_encode([
            "success" => true,
            "message" => "Opération réussie.",
            "data"    => $data
        ]);
    }
}
