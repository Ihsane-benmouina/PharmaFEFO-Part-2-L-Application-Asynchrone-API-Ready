<?php
// src/Controller/Web/DashboardController.php

require_once __DIR__ . '/../../Service/AuthService.php';

class DashboardController
{
    public function __construct() {}

    /**
     * GET index.php?action=dashboard
     * Retourne le squelette HTML — données chargées par dashboard.js via API.
     */
    public function index(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin'], false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/index.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }

    /**
     * GET index.php?action=users
     * Retourne le squelette HTML — données et form via users.js / API.
     */
    public function manageUsers(): void
    {
        AuthService::requireRole('admin', false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/manage_users.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }

    /**
     * GET index.php?action=notifications
     * Retourne le squelette HTML — données chargées par dashboard.js via API.
     */
    public function notifications(): void
    {
        AuthService::requireAnyRole(['pharmacien', 'preparateur', 'admin'], false);

        ob_start();
        include __DIR__ . '/../../../templates/dashboard/notifications.php';
        $content = ob_get_clean();

        include __DIR__ . '/../../../templates/layout/base.php';
    }
}
