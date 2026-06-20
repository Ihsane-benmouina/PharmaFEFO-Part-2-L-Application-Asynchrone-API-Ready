<?php
// public/index.php — Front controller PharmaFEFO Partie 2

require_once __DIR__ . '/../config/environment.php';
require_once __DIR__ . '/../src/Service/AuthService.php';

AuthService::initSession();

$action = $_GET['action'] ?? 'dashboard';
$method = $_SERVER['REQUEST_METHOD'];

// ══════════════════════════════════════════════════════════════
// ROUTAGE API REST  ─ toutes les routes commencent par api/v1/
// ══════════════════════════════════════════════════════════════
if (strpos($action, 'api/v1/') === 0) {

    require_once __DIR__ . '/../src/Controller/Api/ApiAuthController.php';
    require_once __DIR__ . '/../src/Controller/Api/ApiStockController.php';
    require_once __DIR__ . '/../src/Controller/Api/ApiDashboardController.php';
    require_once __DIR__ . '/../src/Controller/Api/ApiUsersController.php';
    require_once __DIR__ . '/../src/Controller/Api/ApiReportController.php';

    // ── Auth ─────────────────────────────────────────────────
    if ($action === 'api/v1/login' && $method === 'POST') {
        (new ApiAuthController())->login();
        exit;
    }

    if ($action === 'api/v1/me' && $method === 'GET') {
        (new ApiAuthController())->me();
        exit;
    }

    // ── Produits ──────────────────────────────────────────────
    if ($action === 'api/v1/products' && $method === 'GET') {
        (new ApiStockController())->listProducts();
        exit;
    }

    // ── Lots — routes spécifiques AVANT les routes génériques ─
    // IMPORTANT : checkout/direct avant checkout, fefo avant {id}/expire

    if ($action === 'api/v1/batches/checkout/direct' && $method === 'POST') {
        (new ApiStockController())->checkoutDirect();
        exit;
    }

    if ($action === 'api/v1/batches/checkout' && $method === 'POST') {
        (new ApiStockController())->checkout();
        exit;
    }

    if ($action === 'api/v1/batches/fefo' && $method === 'GET') {
        (new ApiStockController())->getFefoBatch();
        exit;
    }

    if (preg_match('/^api\/v1\/batches\/(\d+)\/expire$/', $action, $m) && $method === 'PATCH') {
        (new ApiStockController())->markExpired((int)$m[1]);
        exit;
    }

    // Route générique lots (liste + création) — EN DERNIER parmi les routes /batches
    if ($action === 'api/v1/batches') {
        $ctrl = new ApiStockController();
        if ($method === 'GET')       $ctrl->listBatches();
        elseif ($method === 'POST')  $ctrl->createBatch();
        else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        }
        exit;
    }

    // ── Dashboard ─────────────────────────────────────────────
    if ($action === 'api/v1/dashboard/alerts' && $method === 'GET') {
        (new ApiDashboardController())->getAlerts();
        exit;
    }

    if ($action === 'api/v1/dashboard/notifications' && $method === 'GET') {
        (new ApiReportController())->notifications();
        exit;
    }

    // ── Utilisateurs ──────────────────────────────────────────
    if ($action === 'api/v1/users') {
        $ctrl = new ApiUsersController();
        if ($method === 'GET')       $ctrl->listUsers();
        elseif ($method === 'POST')  $ctrl->createUser();
        else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
        }
        exit;
    }

    // ── Rapport financier ─────────────────────────────────────
    if ($action === 'api/v1/report/loss' && $method === 'GET') {
        (new ApiReportController())->financialLoss();
        exit;
    }

    // ── Endpoint inconnu ──────────────────────────────────────
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Endpoint API introuvable : ' . htmlspecialchars($action)]);
    exit;
}

// ══════════════════════════════════════════════════════════════
// ROUTAGE WEB — retourne du HTML via les Controllers Web
// ══════════════════════════════════════════════════════════════
if (!AuthService::isAuthenticated() && $action !== 'login') {
    header('Location: index.php?action=login');
    exit;
}

switch ($action) {

    case 'login':
        require_once __DIR__ . '/../src/Controller/Web/AuthController.php';
        (new AuthController())->login();
        break;

    case 'logout':
        require_once __DIR__ . '/../src/Controller/Web/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/../src/Controller/Web/DashboardController.php';
        (new DashboardController())->index();
        break;

    case 'users':
        require_once __DIR__ . '/../src/Controller/Web/DashboardController.php';
        (new DashboardController())->manageUsers();
        break;

    case 'add-batch':
        require_once __DIR__ . '/../src/Controller/Web/StockController.php';
        (new StockController())->addBatch();
        break;

    case 'dispense':
        require_once __DIR__ . '/../src/Controller/Web/StockController.php';
        (new StockController())->dispense();
        break;

    case 'report':
        require_once __DIR__ . '/../src/Controller/Web/AdminController.php';
        (new AdminController())->reports();
        break;

    case 'notifications':
        require_once __DIR__ . '/../src/Controller/Web/DashboardController.php';
        (new DashboardController())->notifications();
        break;

    default:
        http_response_code(404);
        echo '<div style="padding:20px;font-family:sans-serif"><strong>Erreur 404 :</strong> Page introuvable.</div>';
        break;
}
