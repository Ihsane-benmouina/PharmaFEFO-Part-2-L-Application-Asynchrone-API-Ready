<?php
// src/Controller/Api/ApiStockController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Service/StockService.php';

class ApiStockController
{
    private StockService $stockService;

    public function __construct()
    {
        header('Content-Type: application/json');
        $this->stockService = new StockService();
    }
     public function createBatch(): void
    {
        AuthService::requireAnyRole(['preparateur', 'admin']);

        $productId  = isset($_POST['produit_id'])      ? (int)$_POST['produit_id']  : 0;
        $lotNumber  = trim($_POST['numero_lot']        ?? '');
        $quantity   = isset($_POST['quantite'])        ? (int)$_POST['quantite']    : 0;
        $expiryDate = trim($_POST['date_peremption']   ?? '');

        if (!$productId || empty($lotNumber) || $quantity <= 0 || empty($expiryDate)) {
            http_response_code(422);
            echo json_encode([
                "success" => false,
                "message" => "Données de formulaire incomplètes ou invalides."
            ]);
            return;
        }

        $result = $this->stockService->createBatch($productId, $lotNumber, $quantity, $expiryDate);

        if ($result) {
            echo json_encode([
                "success" => true,
                "message" => "Le lot a été classé précisément dans la file d'attente FEFO !",
                "data"    => null
            ]);
        } else {
            http_response_code(422);
            echo json_encode([
                "success" => false,
                "message" => "La date de péremption est antérieure à la date du jour ou données invalides."
            ]);
        }
    }
}