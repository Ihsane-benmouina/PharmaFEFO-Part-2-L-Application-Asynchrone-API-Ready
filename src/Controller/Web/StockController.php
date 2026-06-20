<?php
// src/Controller/Web/StockController.php

require_once __DIR__ . '/../../Service/AuthService.php';

class StockController
{
    public function __construct() {}

    /**
     * GET index.php?action=add-batch
     * Squelette HTML — produits et soumission gérés par dashboard.js via API.
     */
    public function addBatch(): void
    {
        AuthService::requireAnyRole(['preparateur', 'admin'], false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/add-batch.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }

    /**
     * GET index.php?action=dispense
     * Squelette HTML — produits, lot FEFO et déstockage gérés par dashboard.js via API.
     */
    public function dispense(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin'], false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/sortie.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }
}
