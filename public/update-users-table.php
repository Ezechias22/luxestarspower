<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "<h1>Mise à jour de la base de données</h1>";
    echo "<pre>";
    
    // Ajoute les colonnes shop
    echo "✅ Ajout des colonnes boutique...\n";
    $db->query("ALTER TABLE users ADD COLUMN shop_name VARCHAR(255) NULL AFTER role");
    $db->query("ALTER TABLE users ADD COLUMN shop_slug VARCHAR(255) NULL UNIQUE AFTER shop_name");
    $db->query("ALTER TABLE users ADD COLUMN shop_description TEXT NULL AFTER shop_slug");
    $db->query("ALTER TABLE users ADD COLUMN shop_logo VARCHAR(500) NULL AFTER shop_description");
    $db->query("ALTER TABLE users ADD COLUMN shop_banner VARCHAR(500) NULL AFTER shop_logo");
    
    echo "✅ Colonnes ajoutées avec succès !\n\n";
    
    // Crée la table shop_visits pour les statistiques
    echo "✅ Création de la table shop_visits...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS shop_visits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            seller_id INT NOT NULL,
            visitor_ip VARCHAR(45),
            user_agent TEXT,
            referrer VARCHAR(500),
            visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    
    echo "✅ Table shop_visits créée !\n\n";
    
    echo "🎉 Mise à jour terminée avec succès !\n";
    echo "🗑️  Supprime ce fichier après : public/update-users-table.php\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>❌ ERREUR</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>