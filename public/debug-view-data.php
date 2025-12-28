<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

session_start();

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "🔍 DEBUG VUE - Données passées à la vue\n";
echo "========================================\n\n";

use App\Controllers\ShopController;
use App\Database;

// Intercepte les données passées à view()
$GLOBALS['debug_view_data'] = null;

// Override de la fonction view()
function view($template, $data = []) {
    $GLOBALS['debug_view_data'] = $data;
    echo "✅ Fonction view() appelée\n";
    echo "Template: $template\n\n";
}

try {
    $controller = new ShopController();
    
    echo "Appel de ShopController::show(['slug' => 'zeko-boutique'])\n";
    echo "---------------------------------------------------------------\n\n";
    
    $controller->show(['slug' => 'zeko-boutique']);
    
    echo "\n========================================\n";
    echo "📦 DONNÉES PASSÉES À LA VUE\n";
    echo "========================================\n\n";
    
    if ($GLOBALS['debug_view_data']) {
        $data = $GLOBALS['debug_view_data'];
        
        echo "seller:\n";
        if (isset($data['seller'])) {
            echo "  - id: {$data['seller']['id']}\n";
            echo "  - name: {$data['seller']['name']}\n";
            echo "  - shop_name: {$data['seller']['shop_name']}\n";
        } else {
            echo "  ❌ MANQUANT\n";
        }
        
        echo "\nproducts:\n";
        if (isset($data['products'])) {
            echo "  - Type: " . gettype($data['products']) . "\n";
            echo "  - Count: " . count($data['products']) . "\n";
            echo "  - empty(): " . (empty($data['products']) ? 'TRUE' : 'FALSE') . "\n\n";
            
            if (!empty($data['products'])) {
                echo "  Liste des produits:\n";
                foreach ($data['products'] as $idx => $p) {
                    echo "    [$idx] ID: {$p['id']}, Title: {$p['title']}\n";
                }
            } else {
                echo "  ⚠️ TABLEAU VIDE !\n";
            }
        } else {
            echo "  ❌ VARIABLE MANQUANTE !\n";
        }
        
        echo "\nstats:\n";
        if (isset($data['stats'])) {
            echo "  - products_count: {$data['stats']['products_count']}\n";
            echo "  - sales_count: {$data['stats']['sales_count']}\n";
        } else {
            echo "  ❌ MANQUANT\n";
        }
        
        echo "\n\n========================================\n";
        echo "🔍 TEST DIRECT DATABASE\n";
        echo "========================================\n\n";
        
        $db = Database::getInstance();
        $directProducts = $db->fetchAll("SELECT * FROM products WHERE seller_id = 2");
        
        echo "SELECT * FROM products WHERE seller_id = 2\n";
        echo "Résultat: " . count($directProducts) . " produit(s)\n\n";
        
        if (!empty($directProducts)) {
            echo "Produits trouvés:\n";
            foreach ($directProducts as $p) {
                echo "  - ID: {$p['id']}, seller_id: {$p['seller_id']}, title: {$p['title']}\n";
            }
        }
        
    } else {
        echo "❌ Aucune donnée capturée\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "FIN DU DIAGNOSTIC\n";
echo "========================================\n";