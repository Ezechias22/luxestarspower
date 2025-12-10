<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Charger les variables d'environnement
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Initialiser l'application
$router = new App\Core\Router();

// Démarrer l'application
$router->dispatch();