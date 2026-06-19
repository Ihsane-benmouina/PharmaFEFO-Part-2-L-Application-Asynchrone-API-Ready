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
    }
}