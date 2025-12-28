<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

session_start();

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Test ShopController</h1>";
echo "<pre>";

use App\Controllers\ShopController;

try {
    echo "Création du ShopController...\n";
    $controller = new ShopController();
    echo "✅ Controller créé\n\n";
    
    echo "Appel de show() avec slug = 'zeko-boutique'...\n";
    
    ob_start();
    $controller->show(['slug' => 'zeko-boutique']);
    $output = ob_get_clean();
    
    echo "✅ Méthode show() exécutée\n\n";
    
    echo "========================================\n";
    echo "RENDU HTML:\n";
    echo "========================================\n";
    echo "</pre>";
    echo $output;
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
    echo "</pre>";
}