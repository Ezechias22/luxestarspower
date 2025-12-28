<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

$shopSlug = $_GET['shop'] ?? 'zeko-boutique';

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "🔍 DEBUG BOUTIQUE: $shopSlug\n";
echo "========================================\n\n";

try {
    $db = Database::getInstance();
    
    // ===== ÉTAPE 1: Trouve le vendeur =====
    echo "ÉTAPE 1: Recherche du vendeur\n";
    echo "--------------------------------\n";
    
    $seller = $db->fetchOne(
        "SELECT * FROM users WHERE (shop_slug = ? OR store_slug = ?) AND role = 'seller'",
        [$shopSlug, $shopSlug]
    );
    
    if (!$seller) {
        echo "❌ VENDEUR INTROUVABLE !\n";
        exit;
    }
    
    echo "✅ Vendeur trouvé:\n";
    echo "  ID: {$seller['id']}\n";
    echo "  Type ID: " . gettype($seller['id']) . "\n";
    echo "  Nom: {$seller['name']}\n";
    echo "  shop_slug: {$seller['shop_slug']}\n";
    echo "  store_slug: {$seller['store_slug']}\n\n";
    
    $sellerId = $seller['id'];
    
    // ===== ÉTAPE 2: Vérifie le type de seller_id dans products =====
    echo "ÉTAPE 2: Structure de la table products\n";
    echo "----------------------------------------\n";
    
    $columns = $db->fetchAll("DESCRIBE products");
    foreach ($columns as $col) {
        if ($col['Field'] === 'seller_id') {
            echo "seller_id column:\n";
            echo "  Type: {$col['Type']}\n";
            echo "  Null: {$col['Null']}\n";
            echo "  Key: {$col['Key']}\n";
            echo "  Default: {$col['Default']}\n";
            echo "  Extra: {$col['Extra']}\n\n";
        }
    }
    
    // ===== ÉTAPE 3: Test différentes requêtes =====
    echo "ÉTAPE 3: Test des requêtes SQL\n";
    echo "--------------------------------\n";
    
    // Test 1: Sans CAST
    echo "Test 1: SELECT * FROM products WHERE seller_id = $sellerId\n";
    $test1 = $db->fetchAll("SELECT id, title, seller_id, is_active FROM products WHERE seller_id = ?", [$sellerId]);
    echo "Résultat: " . count($test1) . " produit(s)\n\n";
    
    // Test 2: Avec CAST sur seller_id (dans products)
    echo "Test 2: SELECT * FROM products WHERE CAST(seller_id AS CHAR) = '$sellerId'\n";
    $test2 = $db->fetchAll("SELECT id, title, seller_id, is_active FROM products WHERE CAST(seller_id AS CHAR) = ?", [(string)$sellerId]);
    echo "Résultat: " . count($test2) . " produit(s)\n\n";
    
    // Test 3: Avec CAST sur les deux
    echo "Test 3: SELECT * FROM products WHERE CAST(seller_id AS CHAR) = CAST($sellerId AS CHAR)\n";
    $test3 = $db->fetchAll("SELECT id, title, seller_id, is_active FROM products WHERE CAST(seller_id AS CHAR) = CAST(? AS CHAR)", [(string)$sellerId]);
    echo "Résultat: " . count($test3) . " produit(s)\n\n";
    
    // Test 4: Tous les produits
    echo "Test 4: SELECT * FROM products (TOUS)\n";
    $allProducts = $db->fetchAll("SELECT id, title, seller_id, is_active FROM products LIMIT 10");
    echo "Total produits dans la base: " . count($allProducts) . "\n";
    if (!empty($allProducts)) {
        echo "\nÉchantillon de produits:\n";
        foreach ($allProducts as $p) {
            $sellerIdType = gettype($p['seller_id']);
            $match = ($p['seller_id'] == $sellerId) ? '✅ MATCH' : '❌ NO MATCH';
            echo "  - ID: {$p['id']}, seller_id: {$p['seller_id']} (type: $sellerIdType), $match\n";
        }
    }
    echo "\n";
    
    // ===== ÉTAPE 4: Test getBySeller du repository =====
    echo "ÉTAPE 4: Test ProductRepository::getBySeller()\n";
    echo "------------------------------------------------\n";
    
    $productRepo = new \App\Repositories\ProductRepository();
    $products = $productRepo->getBySeller($sellerId);
    
    echo "ProductRepository::getBySeller($sellerId) retourne: " . count($products) . " produit(s)\n\n";
    
    if (!empty($products)) {
        echo "Produits trouvés:\n";
        foreach ($products as $p) {
            echo "  - {$p['title']} (ID: {$p['id']}, seller_id: {$p['seller_id']})\n";
        }
    }
    
    // ===== ÉTAPE 5: Test ShopController =====
    echo "\n\nÉTAPE 5: Simulation ShopController\n";
    echo "------------------------------------\n";
    
    $userRepo = new \App\Repositories\UserRepository();
    $testSeller = $userRepo->findByShopSlug($shopSlug);
    
    if ($testSeller) {
        echo "UserRepository::findByShopSlug('$shopSlug') ✅\n";
        echo "  seller_id retourné: {$testSeller['id']} (type: " . gettype($testSeller['id']) . ")\n\n";
        
        $testProducts = $productRepo->getBySeller($testSeller['id']);
        echo "ProductRepository::getBySeller({$testSeller['id']}) = " . count($testProducts) . " produit(s)\n";
    }
    
    // ===== RECOMMANDATIONS =====
    echo "\n\n========================================\n";
    echo "📋 RECOMMANDATIONS\n";
    echo "========================================\n";
    
    if (count($test1) > 0) {
        echo "✅ La requête SANS CAST fonctionne !\n";
        echo "→ Supprime le CAST de ProductRepository::getBySeller()\n\n";
    } elseif (count($test2) > 0) {
        echo "✅ La requête AVEC CAST fonctionne !\n";
        echo "→ Le CAST est nécessaire\n\n";
    } else {
        echo "❌ AUCUNE requête ne fonctionne !\n";
        echo "→ Le problème est ailleurs\n\n";
        
        if (count($allProducts) == 0) {
            echo "⚠️ Aucun produit dans la base de données\n";
            echo "→ Crée des produits d'abord\n";
        } else {
            echo "⚠️ Les seller_id ne correspondent pas\n";
            echo "→ Vérifie la cohérence des données\n";
        }
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "FIN DU DIAGNOSTIC\n";
echo "========================================\n";