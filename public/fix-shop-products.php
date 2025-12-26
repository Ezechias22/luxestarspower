<?php
/**
 * 🔧 DIAGNOSTIC ET CORRECTION - PRODUITS BOUTIQUE
 * 
 * URL: https://luxestarspower.com/fix-shop-products.php?secret=luxestar2025&shop=zeko-boutique
 */

$SECRET_KEY = 'luxestar2025';

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    http_response_code(403);
    die('🔒 Accès refusé');
}

$shopSlug = $_GET['shop'] ?? '';

if (empty($shopSlug)) {
    die('⚠️ Paramètre ?shop=xxx requis');
}

header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Diagnostic Boutique</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(0,0,0,0.3);
            border-radius: 15px;
            padding: 30px;
            backdrop-filter: blur(10px);
        }
        .section {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }
        .success { color: #4caf50; }
        .error { color: #f44336; }
        .warning { color: #ff9800; }
        .info { color: #2196f3; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            background: rgba(0,0,0,0.2);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        th {
            background: rgba(255,255,255,0.1);
            font-weight: bold;
        }
        .code {
            background: rgba(0,0,0,0.5);
            padding: 10px;
            border-radius: 5px;
            font-family: monospace;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 DIAGNOSTIC BOUTIQUE: <?php echo htmlspecialchars($shopSlug); ?></h1>
        
<?php

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // ========== ÉTAPE 1: Trouve le vendeur ==========
    echo '<div class="section">';
    echo '<h2>👤 ÉTAPE 1: Recherche du vendeur</h2>';
    
    $seller = $db->fetchOne(
        "SELECT * FROM users 
         WHERE (shop_slug = ? OR store_slug = ?) AND role = 'seller'",
        [$shopSlug, $shopSlug]
    );
    
    if (!$seller) {
        echo '<p class="error">❌ VENDEUR INTROUVABLE avec ce slug !</p>';
        
        // Cherche par similarité
        echo '<p class="info">🔍 Recherche de vendeurs similaires...</p>';
        $similar = $db->fetchAll(
            "SELECT id, name, email, shop_slug, store_slug FROM users WHERE role = 'seller' ORDER BY created_at DESC LIMIT 10"
        );
        
        if (!empty($similar)) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nom</th><th>shop_slug</th><th>store_slug</th></tr>';
            foreach ($similar as $s) {
                echo '<tr>';
                echo '<td>' . $s['id'] . '</td>';
                echo '<td>' . htmlspecialchars($s['name']) . '</td>';
                echo '<td><code>' . htmlspecialchars($s['shop_slug'] ?? 'NULL') . '</code></td>';
                echo '<td><code>' . htmlspecialchars($s['store_slug'] ?? 'NULL') . '</code></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
        die('</div></div></body></html>');
    }
    
    echo '<p class="success">✅ Vendeur trouvé !</p>';
    echo '<table>';
    echo '<tr><th>Champ</th><th>Valeur</th></tr>';
    echo '<tr><td>ID</td><td><strong>' . $seller['id'] . '</strong></td></tr>';
    echo '<tr><td>Nom</td><td>' . htmlspecialchars($seller['name']) . '</td></tr>';
    echo '<tr><td>Email</td><td>' . htmlspecialchars($seller['email']) . '</td></tr>';
    echo '<tr><td>shop_slug</td><td><code>' . htmlspecialchars($seller['shop_slug'] ?? 'NULL') . '</code></td></tr>';
    echo '<tr><td>store_slug</td><td><code>' . htmlspecialchars($seller['store_slug'] ?? 'NULL') . '</code></td></tr>';
    echo '</table>';
    echo '</div>';
    
    $sellerId = $seller['id'];
    
    // ========== ÉTAPE 2: Cherche les produits ==========
    echo '<div class="section">';
    echo '<h2>📦 ÉTAPE 2: Recherche des produits du vendeur</h2>';
    echo '<p class="info">Recherche avec seller_id = ' . $sellerId . '</p>';
    
    $products = $db->fetchAll(
        "SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC",
        [$sellerId]
    );
    
    echo '<p><strong>Produits trouvés: ' . count($products) . '</strong></p>';
    
    if (empty($products)) {
        echo '<p class="warning">⚠️ AUCUN PRODUIT trouvé pour ce seller_id !</p>';
        
        // Vérifie s'il y a des produits orphelins
        echo '<p class="info">🔍 Recherche de tous les produits...</p>';
        $allProducts = $db->fetchAll(
            "SELECT p.*, u.name as seller_name, u.id as real_seller_id 
             FROM products p 
             LEFT JOIN users u ON p.seller_id = u.id 
             ORDER BY p.created_at DESC 
             LIMIT 20"
        );
        
        if (!empty($allProducts)) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Titre</th><th>seller_id</th><th>Vendeur</th><th>Actif</th><th>Status</th></tr>';
            foreach ($allProducts as $p) {
                $highlight = ($p['seller_name'] && stripos($p['seller_name'], 'Zeko') !== false) ? 'style="background: rgba(255,193,7,0.3);"' : '';
                echo '<tr ' . $highlight . '>';
                echo '<td>' . $p['id'] . '</td>';
                echo '<td>' . htmlspecialchars($p['title']) . '</td>';
                echo '<td><strong>' . $p['seller_id'] . '</strong></td>';
                echo '<td>' . htmlspecialchars($p['seller_name'] ?? 'NULL') . '</td>';
                echo '<td>' . ($p['is_active'] ? '✅' : '❌') . '</td>';
                echo '<td>' . htmlspecialchars($p['status'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        
    } else {
        echo '<table>';
        echo '<tr><th>ID</th><th>Titre</th><th>is_active</th><th>status</th><th>Prix</th></tr>';
        foreach ($products as $p) {
            $statusColor = $p['is_active'] ? 'success' : 'error';
            echo '<tr>';
            echo '<td>' . $p['id'] . '</td>';
            echo '<td>' . htmlspecialchars($p['title']) . '</td>';
            echo '<td class="' . $statusColor . '">' . ($p['is_active'] ? '✅ Actif' : '❌ Inactif') . '</td>';
            echo '<td>' . htmlspecialchars($p['status'] ?? 'NULL') . '</td>';
            echo '<td>$' . number_format($p['price'], 2) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    
    echo '</div>';
    
    // ========== ÉTAPE 3: Test de la requête ShopController ==========
    echo '<div class="section">';
    echo '<h2>🔍 ÉTAPE 3: Simulation requête ShopController</h2>';
    
    $testProducts = $db->fetchAll(
        "SELECT * FROM products 
         WHERE seller_id = ? 
         AND is_active = 1 
         ORDER BY created_at DESC 
         LIMIT 50",
        [$sellerId]
    );
    
    echo '<p><strong>Résultat de la requête ShopController:</strong></p>';
    echo '<div class="code">';
    echo 'SELECT * FROM products <br>';
    echo 'WHERE seller_id = ' . $sellerId . '<br>';
    echo 'AND is_active = 1<br>';
    echo 'ORDER BY created_at DESC LIMIT 50';
    echo '</div>';
    
    echo '<p class="' . (count($testProducts) > 0 ? 'success' : 'error') . '">';
    echo count($testProducts) . ' produit(s) retourné(s)';
    echo '</p>';
    
    if (empty($testProducts) && !empty($products)) {
        echo '<p class="warning">⚠️ PROBLÈME DÉTECTÉ: Des produits existent mais is_active = 0</p>';
        
        if (isset($_GET['fix']) && $_GET['fix'] === 'yes') {
            echo '<p class="info">🔧 Activation des produits...</p>';
            $pdo->exec("UPDATE products SET is_active = 1 WHERE seller_id = $sellerId");
            echo '<p class="success">✅ Produits activés ! <a href="?secret=' . $SECRET_KEY . '&shop=' . $shopSlug . '" style="color: #4caf50;">Rafraîchir</a></p>';
        } else {
            echo '<p><a href="?secret=' . $SECRET_KEY . '&shop=' . $shopSlug . '&fix=yes" style="color: #ff9800; font-weight: bold;">🔧 Cliquez ici pour activer les produits</a></p>';
        }
    }
    
    echo '</div>';
    
    // ========== ÉTAPE 4: URL de test ==========
    echo '<div class="section">';
    echo '<h2>🌐 ÉTAPE 4: URL de test</h2>';
    $url = 'https://luxestarspower.com/boutique/' . $shopSlug;
    echo '<p>URL de la boutique:</p>';
    echo '<p><a href="' . $url . '" target="_blank" style="color: #4caf50; font-size: 1.2rem; font-weight: bold;">' . $url . '</a></p>';
    echo '</div>';
    
} catch (\Exception $e) {
    echo '<div class="section">';
    echo '<p class="error">❌ ERREUR: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre style="background: rgba(0,0,0,0.5); padding: 15px; border-radius: 5px; overflow-x: auto;">';
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
    echo '</div>';
}

?>
        
        <hr style="margin: 30px 0; border: 1px solid rgba(255,255,255,0.3);">
        <p style="text-align: center;">
            <a href="javascript:if(confirm('Supprimer ce fichier ?')) { window.location.href = '?secret=<?php echo $SECRET_KEY; ?>&shop=<?php echo $shopSlug; ?>&delete=yes'; }" 
               style="color: #f44336; text-decoration: none; font-weight: bold;">
                🗑️ Supprimer ce fichier de diagnostic
            </a>
        </p>
    </div>
</body>
</html>

<?php
if (isset($_GET['delete']) && $_GET['delete'] === 'yes') {
    if (unlink(__FILE__)) {
        echo '<p class="success">✅ Fichier supprimé !</p>';
    }
}
?>