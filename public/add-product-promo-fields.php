<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "========================================\n";
    echo "🔧 AJOUT DES COLONNES PROMOTION\n";
    echo "========================================\n\n";
    
    $columns = [
        // Prix promotionnel
        'original_price' => "ALTER TABLE products ADD COLUMN original_price DECIMAL(10,2) DEFAULT NULL AFTER price",
        'discount_percentage' => "ALTER TABLE products ADD COLUMN discount_percentage INT DEFAULT 0 AFTER original_price",
        'is_on_sale' => "ALTER TABLE products ADD COLUMN is_on_sale TINYINT(1) DEFAULT 0 AFTER discount_percentage",
        'sale_starts_at' => "ALTER TABLE products ADD COLUMN sale_starts_at DATETIME DEFAULT NULL AFTER is_on_sale",
        'sale_ends_at' => "ALTER TABLE products ADD COLUMN sale_ends_at DATETIME DEFAULT NULL AFTER sale_starts_at",
        
        // Objectif de ventes (pour la barre de progression)
        'sales_goal' => "ALTER TABLE products ADD COLUMN sales_goal INT DEFAULT 100 AFTER sales",
    ];
    
    foreach ($columns as $col => $sql) {
        try {
            $db->query($sql);
            echo "✅ Colonne '$col' ajoutée\n";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⏭️  Colonne '$col' existe déjà\n";
            } else {
                echo "❌ Erreur '$col': " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Migration terminée !\n\n";
    
    echo "Les nouvelles colonnes:\n";
    echo "- original_price: Prix d'origine avant réduction\n";
    echo "- discount_percentage: Pourcentage de réduction (0-100)\n";
    echo "- is_on_sale: Le produit est en promotion (0/1)\n";
    echo "- sale_starts_at: Date de début de la promotion\n";
    echo "- sale_ends_at: Date de fin de la promotion\n";
    echo "- sales_goal: Objectif de ventes pour la barre de progression\n\n";
    
    echo "Supprime ce script:\n";
    echo "git rm public/add-product-promo-fields.php\n";
    echo "git commit -m 'Remove migration script'\n";
    echo "git push origin main\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}

