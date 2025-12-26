<?php
/**
 * 🔍 DEBUG COMPLET BOUTIQUE
 * URL: https://luxestarspower.com/debug-shop.php?secret=luxestar2025&shop=zeko-boutique
 */

$SECRET_KEY = 'luxestar2025';

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    die('🔒 Accès refusé');
}

$shopSlug = $_GET['shop'] ?? '';
if (empty($shopSlug)) {
    die('⚠️ Paramètre ?shop=xxx requis');
}

header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\Repositories\UserRepository;

echo "========================================\n";
echo "🔍 DEBUG BOUTIQUE: $shopSlug\n";
echo "========================================\n\n";

try {
    $db = Database::getInstance();
    $userRepo = new UserRepository();
    
    // ========== ÉTAPE 1: Trouve le vendeur ==========
    echo "ÉTAPE 1: Recherche du vendeur\n";
    echo "--------------------------------\n";
    
    $seller = $userRepo->findByShopSlug($shopSlug);
    
    if (!$seller) {
        echo "❌ VENDEUR INTROUVABLE !\n";
        echo "\nVendeurs disponibles:\n";
        $sellers = $db->fetchAll("SELECT id, name, shop_slug, store_slug FROM users WHERE role = 'seller'");
        foreach ($sellers as $s) {
            echo "  - ID: {$s['id']}, Nom: {$s['name']}, shop_slug: {$s['shop_slug']}, store_slug: {$s['store_slug']}\n";
        }
        exit;
    }
    
    echo "✅ Vendeur trouvé:\n";
    echo "  ID: {$seller['id']}\n";
    echo "  Nom: {$seller['name']}\n";
    echo "  Email: {$seller['email']}\n";
    echo "  Role: {$seller['role']}\n";
    echo "  shop_slug: {$seller['shop_slug']}\n";
    echo "  store_slug: {$seller['store_slug']}\n\n";
    
    $sellerId = $seller['id'];
    
    // ========== ÉTAPE 2: Tous les produits du vendeur ==========
    echo "ÉTAPE 2: TOUS les produits du vendeur (seller_id = $sellerId)\n";
    echo "----------------------------------------------------------------\n";
    
    $allProducts = $db->fetchAll(
        "SELECT id, title, price, is_active, status, created_at FROM products WHERE seller_id = ?",
        [$sellerId]
    );
    
    echo "Total produits trouvés: " . count($allProducts) . "\n\n";
    
    if (empty($allProducts)) {
        echo "❌ AUCUN PRODUIT pour ce seller_id !\n";
        echo "\nVérifions tous les produits de la base:\n";
        $all = $db->fetchAll("SELECT p.id, p.title, p.seller_id, u.name as seller_name FROM products p LEFT JOIN users u ON p.seller_id = u.id LIMIT 10");
        foreach ($all as $p) {
            echo "  - ID: {$p['id']}, Titre: {$p['title']}, seller_id: {$p['seller_id']}, Vendeur: {$p['seller_name']}\n";
        }
        exit;
    }
    
    foreach ($allProducts as $p) {
        $active = $p['is_active'] ? '✅ OUI' : '❌ NON';
        $status = $p['status'] ?? 'NULL';
        echo "Produit ID: {$p['id']}\n";
        echo "  Titre: {$p['title']}\n";
        echo "  Prix: \${$p['price']}\n";
        echo "  is_active: $active\n";
        echo "  status: $status\n";
        echo "  created_at: {$p['created_at']}\n";
        echo "\n";
    }
    
    // ========== ÉTAPE 3: Test requête EXACTE ShopController ==========
    echo "ÉTAPE 3: Test requête EXACTE du ShopController\n";
    echo "------------------------------------------------\n";
    
    $query1 = "SELECT * FROM products 
               WHERE seller_id = ? 
               AND is_active = 1 
               AND (status IS NULL OR status != 'rejected')
               ORDER BY created_at DESC 
               LIMIT 50";
    
    echo "Requête SQL:\n";
    echo "$query1\n";
    echo "Avec seller_id = $sellerId\n\n";
    
    $testProducts = $db->fetchAll($query1, [$sellerId]);
    
    echo "Résultat: " . count($testProducts) . " produit(s) retourné(s)\n\n";
    
    if (count($testProducts) > 0) {
        echo "✅ Les produits DEVRAIENT s'afficher !\n";
        foreach ($testProducts as $p) {
            echo "  - {$p['title']} (ID: {$p['id']})\n";
        }
    } else {
        echo "❌ AUCUN produit retourné par la requête !\n\n";
        
        // Diagnostic détaillé
        echo "Diagnostic:\n";
        
        $countActive = $db->fetchOne("SELECT COUNT(*) as cnt FROM products WHERE seller_id = ? AND is_active = 1", [$sellerId]);
        echo "  - Produits actifs (is_active=1): {$countActive['cnt']}\n";
        
        $countNull = $db->fetchOne("SELECT COUNT(*) as cnt FROM products WHERE seller_id = ? AND status IS NULL", [$sellerId]);
        echo "  - Produits avec status NULL: {$countNull['cnt']}\n";
        
        $countPending = $db->fetchOne("SELECT COUNT(*) as cnt FROM products WHERE seller_id = ? AND status = 'pending'", [$sellerId]);
        echo "  - Produits avec status pending: {$countPending['cnt']}\n";
        
        $countApproved = $db->fetchOne("SELECT COUNT(*) as cnt FROM products WHERE seller_id = ? AND status = 'approved'", [$sellerId]);
        echo "  - Produits avec status approved: {$countApproved['cnt']}\n";
        
        $countRejected = $db->fetchOne("SELECT COUNT(*) as cnt FROM products WHERE seller_id = ? AND status = 'rejected'", [$sellerId]);
        echo "  - Produits avec status rejected: {$countRejected['cnt']}\n";
    }
    
    // ========== ÉTAPE 4: Vérification du fichier ShopController.php ==========
    echo "\n\nÉTAPE 4: Vérification du code ShopController.php\n";
    echo "--------------------------------------------------\n";
    
    $controllerFile = __DIR__ . '/../app/Controllers/ShopController.php';
    
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        
        // Cherche la requête SQL
        if (preg_match('/SELECT \* FROM products.*?WHERE seller_id.*?;/s', $content, $matches)) {
            echo "Requête trouvée dans le code:\n";
            echo trim($matches[0]) . "\n\n";
            
            if (strpos($content, "status = 'approved'") !== false) {
                echo "⚠️ ATTENTION: Le code contient encore 'status = approved' !\n";
                echo "Le fichier n'a peut-être pas été mis à jour sur le serveur.\n";
            } else if (strpos($content, "status IS NULL OR status != 'rejected'") !== false) {
                echo "✅ Le code contient la bonne condition !\n";
            } else {
                echo "⚠️ Condition de status non identifiée clairement.\n";
            }
        }
    } else {
        echo "❌ Fichier ShopController.php introuvable !\n";
    }
    
    // ========== ÉTAPE 5: Test direct de la vue ==========
    echo "\n\nÉTAPE 5: Informations système\n";
    echo "-------------------------------\n";
    echo "PHP Version: " . phpversion() . "\n";
    echo "Timestamp: " . date('Y-m-d H:i:s') . "\n";
    echo "Railway déploiement: Vérifiez les logs de déploiement\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n\n========================================\n";
echo "FIN DU DIAGNOSTIC\n";
echo "========================================\n";