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
      public function getNextMonthAlertsCount(): int
    {
        $alerts = $this->batchRepo->getNextMonthAlerts();
        return is_array($alerts) ? count($alerts) : 0;
    public function createBatch(int $productId, string $lotNumber, int $quantity, string $expiryDate): bool
    {
        $today     = new DateTime((new DateTime())->format('Y-m-d')); // BUG CORRIGÉ
        $inputDate = new DateTime($expiryDate);

        if ($inputDate < $today) {
            return false;
        }

        return $this->batchRepo->saveInputBatch($productId, $lotNumber, $quantity, $expiryDate);
    }
}