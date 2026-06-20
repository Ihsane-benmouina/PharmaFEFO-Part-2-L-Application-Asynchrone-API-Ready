<?php
// config/environment.php

define('APP_ENV', 'development');

// Signaler toutes les erreurs PHP en interne
error_reporting(E_ALL);

// Détecter si c'est une route API
$isApiRequest = isset($_GET['action']) && strpos($_GET['action'], 'api/v1/') === 0;

if ($isApiRequest) {
    // ESSENTIEL : ne JAMAIS afficher d'erreurs PHP dans la réponse API
    // Une notice/warning avant le JSON le corrompt et provoque "Erreur lors du chargement"
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');

    // Exceptions non catchées → JSON propre (pas de page d'erreur HTML)
    set_exception_handler(function (Throwable $e) {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur : ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')',
        ]);
        exit;
    });

} else {
    // Pages Web : afficher les erreurs normalement en développement
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}
