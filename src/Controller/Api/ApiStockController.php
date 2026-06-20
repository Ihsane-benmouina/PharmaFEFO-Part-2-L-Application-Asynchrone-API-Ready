<?php
// src/Controller/Api/ApiStockController.php

require_once __DIR__ . '/../../Service/AuthService.php';
require_once __DIR__ . '/../../Service/StockService.php';
require_once __DIR__ . '/../../Repository/StockBatchRepository.php';

class ApiStockController
{
    private StockService         $stockService;
    private StockBatchRepository $batchRepo;

    public function __construct()
    {
        header('Content-Type: application/json');
        $this->stockService = new StockService();
        $this->batchRepo    = new StockBatchRepository();
    }

    // =========================================================
    // GET /api/v1/batches[?criteria=all|critical]
    // Liste tous les lots du dashboard avec badge/couleur.
    // Repository utilisé : StockBatchRepository->getDashboardLots()
    // =========================================================
    public function listBatches(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $criteria = $_GET['criteria'] ?? 'all';
        $filter   = ($criteria !== 'all') ? $criteria : null;

        $batches = $this->stockService->processBatchesForApi($filter);

        echo json_encode([
            'success' => true,
            'message' => 'Opération réussie.',
            'data'    => $batches
        ]);
    }

    // =========================================================
    // GET /api/v1/products
    // Liste tous les produits pour les <select>.
    // Repository utilisé : ProductRepository->getAllProducts()
    // =========================================================
    public function listProducts(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $products = $this->stockService->getAllProducts();

        echo json_encode([
            'success' => true,
            'message' => 'Opération réussie.',
            'data'    => $products
        ]);
    }

    // =========================================================
    // POST /api/v1/batches
    // Crée un nouveau lot dans la file FEFO.
    // Repository utilisé : StockBatchRepository->saveInputBatch()
    // =========================================================
    public function createBatch(): void
    {
        AuthService::requireAnyRole(['preparateur', 'admin']);

        $productId  = isset($_POST['produit_id'])    ? (int)$_POST['produit_id']  : 0;
        $lotNumber  = trim($_POST['numero_lot']      ?? '');
        $quantity   = isset($_POST['quantite'])      ? (int)$_POST['quantite']    : 0;
        $expiryDate = trim($_POST['date_peremption'] ?? '');

        if (!$productId || empty($lotNumber) || $quantity <= 0 || empty($expiryDate)) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Données de formulaire incomplètes ou invalides.'
            ]);
            return;
        }

        $result = $this->stockService->createBatch($productId, $lotNumber, $quantity, $expiryDate);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => "Le lot a été classé précisément dans la file d'attente FEFO !",
                'data'    => null
            ]);
        } else {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'La date de péremption est antérieure à la date du jour ou données invalides.'
            ]);
        }
    }

    // =========================================================
    // POST /api/v1/batches/checkout
    // Déstockage FEFO par produit_id (dashboard — bouton "Délivrer").
    // Repository utilisé : StockBatchRepository->getFefoBatchForProduct()
    //                    + StockBatchRepository->dispenseBatch()
    // =========================================================
    public function checkout(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $productId = isset($_POST['produit_id']) ? (int)$_POST['produit_id'] : 0;
        $quantity  = isset($_POST['quantite'])   ? (int)$_POST['quantite']   : 1;

        if (!$productId || $quantity <= 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Identifiant du produit invalide ou quantité incorrecte.'
            ]);
            return;
        }

        $result = $this->stockService->dispenseFefo($productId, $quantity);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Déstockage effectué selon la règle FEFO !',
                'data'    => null
            ]);
        } else {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Stock insuffisant ou aucun lot disponible pour ce produit.'
            ]);
        }
    }

    // =========================================================
    // POST /api/v1/batches/checkout/direct
    // Déstockage direct par lot_id (page Sortie Intelligente).
    // Repository utilisé : StockBatchRepository->dispenseBatch()
    // =========================================================
    public function checkoutDirect(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $lotId    = isset($_POST['lot_id'])   ? (int)$_POST['lot_id']   : 0;
        $quantity = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 0;

        if (!$lotId || $quantity <= 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Identifiant de lot ou quantité invalide.'
            ]);
            return;
        }

        // Appel direct au Repository (règle FEFO déjà appliquée à la sélection du lot)
        $result = $this->batchRepo->dispenseBatch($lotId, $quantity);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Succès US 3.1 : Déstockage effectué selon la règle FEFO (Lot décrémenté en priorité) !',
                'data'    => null
            ]);
        } else {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Stock insuffisant ou lot introuvable.'
            ]);
        }
    }

    // =========================================================
    // PATCH /api/v1/batches/{id}/expire
    // Marque un lot comme EXPIRED, quantité → 0.
    // Repository utilisé : StockBatchRepository->markBatchAsExpired()
    // =========================================================
    public function markExpired(int $id): void
    {
        AuthService::requireAnyRole(['pharmacien', 'admin']);

        if ($id <= 0) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Identifiant de lot invalide.'
            ]);
            return;
        }

        $result = $this->stockService->markBatchAsExpired($id);

        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Lot marqué EXPIRED — quantité remise à zéro.',
                'data'    => [
                    'id'      => $id,
                    'quantite' => 0,
                    'statut'  => 'EXPIRED'
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Impossible de modifier le statut de ce lot.'
            ]);
        }
    }

    // =========================================================
    // GET /api/v1/batches/fefo?produit_id=X
    // Retourne le lot FEFO prioritaire pour un produit donné.
    // Repository utilisé : StockBatchRepository->getFefoBatchForProduct()
    // =========================================================
    public function getFefoBatch(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin']);

        $productId = isset($_GET['produit_id']) ? (int)$_GET['produit_id'] : 0;

        if (!$productId) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Paramètre produit_id requis.'
            ]);
            return;
        }

        // Repository → lot avec la DLU la plus proche (FEFO)
        $lot = $this->batchRepo->getFefoBatchForProduct($productId);

        if (!$lot) {
            echo json_encode([
                'success' => false,
                'message' => 'Aucun lot disponible pour ce produit.',
                'data'    => null
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Lot FEFO trouvé.',
            'data'    => [
                'id'          => (int)$lot['id'],
                'productId'   => (int)$lot['produit_id'],
                'batchNumber' => $lot['numero_lot']      ?? '',
                'quantity'    => (int)$lot['quantite'],
                'expiryDate'  => $lot['date_peremption'] ?? '',
                'status'      => $lot['statut']          ?? 'OK',
            ]
        ]);
    }
}
