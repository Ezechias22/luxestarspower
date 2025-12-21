<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "<h1>Mise à jour de la base de données</h1>";
    echo "<pre>";
    
    // Vérifie si les colonnes existent déjà
    $columns = $db->fetchAll("SHOW COLUMNS FROM users");
    $columnNames = array_column($columns, 'Field');
    
    if (!in_array('shop_name', $columnNames)) {
        echo "✅ Ajout des colonnes boutique...\n";
        $db->query("ALTER TABLE users ADD COLUMN shop_name VARCHAR(255) NULL AFTER role");
        $db->query("ALTER TABLE users ADD COLUMN shop_slug VARCHAR(255) NULL UNIQUE AFTER shop_name");
        $db->query("ALTER TABLE users ADD COLUMN shop_description TEXT NULL AFTER shop_slug");
        $db->query("ALTER TABLE users ADD COLUMN shop_logo VARCHAR(500) NULL AFTER shop_description");
        $db->query("ALTER TABLE users ADD COLUMN shop_banner VARCHAR(500) NULL AFTER shop_logo");
        echo "✅ Colonnes ajoutées avec succès !\n\n";
    } else {
        echo "✅ Les colonnes boutique existent déjà !\n\n";
    }
    
    // Récupère le type de la colonne id de users
    $userIdType = $db->fetchOne("
        SELECT DATA_TYPE, COLUMN_TYPE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'users' 
        AND COLUMN_NAME = 'id'
    ");
    
    echo "ℹ️  Type de users.id : " . $userIdType['COLUMN_TYPE'] . "\n\n";
    
    // Détermine le bon type pour seller_id
    $sellerIdType = $userIdType['COLUMN_TYPE']; // Utilise exactement le même type
    
    // Crée la table shop_visits pour les statistiques
    $tables = $db->fetchAll("SHOW TABLES LIKE 'shop_visits'");
    
    if (empty($tables)) {
        echo "✅ Création de la table shop_visits...\n";
        $db->query("
            CREATE TABLE shop_visits (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                seller_id $sellerIdType NOT NULL,
                visitor_ip VARCHAR(45),
                user_agent TEXT,
                referrer VARCHAR(500),
                visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_seller (seller_id),
                INDEX idx_visited (visited_at),
                FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        echo "✅ Table shop_visits créée !\n\n";
    } else {
        echo "✅ La table shop_visits existe déjà !\n\n";
    }
    
    echo "🎉 Mise à jour terminée avec succès !\n";
    echo "🗑️  Supprime ce fichier après exécution : public/update-users-table.php\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>❌ ERREUR</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "\n<h3>Debug Info:</h3>";
    echo "<pre>";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "</pre>";
}
?>