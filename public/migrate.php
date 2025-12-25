<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Database.php';

// Protection simple
$secret = $_GET['secret'] ?? '';
if ($secret !== 'luxestarsmigrate2025') {
    die('Accès refusé');
}

$db = App\Database::getInstance();

try {
    // Vérifie si la colonne existe déjà
    $columns = $db->fetchAll("DESCRIBE products");
    $viewsExists = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'views') {
            $viewsExists = true;
            break;
        }
    }
    
    if ($viewsExists) {
        echo "⚠️ La colonne 'views' existe déjà !<br>";
    } else {
        // Exécute la migration
        $db->query("ALTER TABLE products ADD COLUMN views INT DEFAULT 0 AFTER is_featured");
        echo "✅ Colonne 'views' ajoutée avec succès !<br>";
    }
    
    // Essaie de créer les index (ignore si ils existent déjà)
    try {
        $db->query("CREATE INDEX idx_products_views ON products(views)");
        echo "✅ Index 'idx_products_views' créé !<br>";
    } catch (Exception $e) {
        echo "⚠️ Index 'idx_products_views' existe déjà<br>";
    }
    
    try {
        $db->query("CREATE INDEX idx_products_seller_views ON products(seller_id, views)");
        echo "✅ Index 'idx_products_seller_views' créé !<br>";
    } catch (Exception $e) {
        echo "⚠️ Index 'idx_products_seller_views' existe déjà<br>";
    }
    
    echo "<br><strong style='color: green;'>Migration terminée avec succès !</strong>";
    echo "<br><br><a href='/'>Retour à l'accueil</a>";
    
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>