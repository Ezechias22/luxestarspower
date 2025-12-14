<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

$db = Database::getInstance();

try {
    // Vérifie si la colonne existe déjà
    $result = $db->fetchOne("SHOW COLUMNS FROM products LIKE 'views'");
    
    if (!$result) {
        // Ajoute la colonne
        $db->query("ALTER TABLE products ADD COLUMN views INT UNSIGNED DEFAULT 0 NOT NULL AFTER price");
        $db->query("CREATE INDEX idx_views ON products(views)");
        
        echo "✅ Colonne 'views' ajoutée avec succès !";
    } else {
        echo "ℹ️ Colonne 'views' existe déjà.";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage();
}