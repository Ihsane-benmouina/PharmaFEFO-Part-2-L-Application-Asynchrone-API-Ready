<?php
// config/database.php

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=localhost;dbname=pharmafefo_db;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            } catch (PDOException $e) {
                // Détecter si c'est une route API pour retourner du JSON au lieu de die()
                $isApi = isset($_GET['action']) && strpos($_GET['action'], 'api/v1/') === 0;

                if ($isApi) {
                    if (ob_get_length()) ob_clean();
                    header('Content-Type: application/json');
                    http_response_code(503);
                    echo json_encode([
                        'success' => false,
                        'message' => 'Connexion base de données impossible : ' . $e->getMessage(),
                    ]);
                    exit;
                }

                // Page Web : affichage lisible
                http_response_code(503);
                die('<div style="padding:20px;font-family:sans-serif;color:#991b1b;background:#fef2f2;border-radius:8px;margin:20px">
                    <strong>Erreur de connexion BDD :</strong> ' . htmlspecialchars($e->getMessage()) . '
                    <br><small>Vérifiez que MySQL est démarré et que la base <code>pharmafefo_db</code> existe.</small>
                </div>');
            }
        }

        return self::$instance;
    }
}
