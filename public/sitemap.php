<?php
require __DIR__ . '/../vendor/autoload.php';

// Charge .env si existe
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Appelle le contrôleur
$controller = new \App\Controllers\SitemapController();
$controller->generate([]);