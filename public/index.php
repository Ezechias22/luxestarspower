<?php

/**
 * LuxeStarsPower - Application Entry Point
 */

// Charger l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Charger .env seulement s'il existe (Railway utilise des variables d'environnement)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Configuration des erreurs
$isProduction = (getenv('APP_ENV') ?: $_ENV['APP_ENV'] ?? 'development') === 'production';

if ($isProduction) {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Timezone
date_default_timezone_set('UTC');

// Sessions sécurisées
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.use_strict_mode', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de sécurité
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Charger les helpers
require_once __DIR__ . '/../app/helpers.php';

// Initialiser le routeur
$router = new \App\Router();

// Charger les routes
require_once __DIR__ . '/../routes.php';

// Exécuter le routeur
try {
    $router->dispatch();
} catch (Exception $e) {
    if ($isProduction) {
        http_response_code(500);
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    } else {
        echo "<h1>Erreur</h1>";
        echo "<pre>";
        echo "Message: " . $e->getMessage() . "\n";
        echo "Fichier: " . $e->getFile() . "\n";
        echo "Ligne: " . $e->getLine() . "\n\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    }
    exit(1);
}