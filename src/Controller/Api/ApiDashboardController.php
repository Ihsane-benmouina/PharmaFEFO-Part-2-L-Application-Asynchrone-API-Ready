<?php
// src/Controller/Api/ApiDashboardController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Service/StockService.php';

class ApiDashboardController
{
    private StockService $stockService;

    public function __construct()
    {
        header('Content-Type: application/json');
        $this->stockService = new StockService();
    }

  
    public function getAlerts(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $count = $this->stockService->getNextMonthAlertsCount();

        echo json_encode([
            "success" => true,
            "message" => "Opération réussie",
            "data"    => [
                "expiringNextMonth" => $count
            ]
        ]);
    }
}
