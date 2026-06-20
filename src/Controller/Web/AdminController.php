<?php
// src/Controller/Web/AdminController.php

require_once __DIR__ . '/../../Service/AuthService.php';

class AdminController
{
    public function __construct() {}

    /**
     * GET index.php?action=report
     * Squelette HTML — total financier chargé par dashboard.js via GET /api/v1/report/loss.
     */
    public function reports(): void
    {
        AuthService::requireRole('admin', false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/report.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }
}
