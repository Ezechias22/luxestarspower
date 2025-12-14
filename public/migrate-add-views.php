<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "🔄 Vérification de la colonne 'views'...\n\n";
    
    // Vérifie si la colonne existe
    $check = $db->fetchOne("SHOW COLUMNS FROM products LIKE 'views'");
    
    if (!$check) {
        echo "➕ Ajout de la colonne 'views'...\n";
        $db->query("ALTER TABLE products ADD COLUMN views INT UNSIGNED DEFAULT 0 NOT NULL AFTER price");
        echo "✅ Colonne 'views' ajoutée avec succès !\n\n";
        
        // Ajoute l'index
        echo "➕ Création de l'index idx_views...\n";
        $db->query("CREATE INDEX idx_views ON products(views)");
        echo "✅ Index créé avec succès !\n\n";
    } else {
        echo "✓ La colonne 'views' existe déjà.\n\n";
    }
    
    // Vérifie la structure finale
    echo "📋 Structure de la colonne 'views' :\n";
    $result = $db->fetchOne("SHOW COLUMNS FROM products LIKE 'views'");
    echo "   Type: {$result['Type']}\n";
    echo "   Null: {$result['Null']}\n";
    echo "   Default: {$result['Default']}\n\n";
    
    // Compte les produits
    $count = $db->fetchOne("SELECT COUNT(*) as total FROM products");
    echo "📦 Total de produits : {$count['total']}\n\n";
    
    echo "✅ Migration terminée avec succès !\n";
    echo "🗑️  N'oubliez pas de supprimer ce fichier : public/migrate-add-views.php\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "\n📝 Détails:\n";
    echo $e->getTraceAsString();
}
?>