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
    echo "🔧 AJOUT DE LA COLONNE SALES\n";
    echo "========================================\n\n";
    
    // Ajoute d'abord la colonne 'sales' si elle n'existe pas
    try {
        $db->query("ALTER TABLE products ADD COLUMN sales INT DEFAULT 0 AFTER views");
        echo "✅ Colonne 'sales' ajoutée\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "⏭️  Colonne 'sales' existe déjà\n";
        } else {
            echo "❌ Erreur 'sales': " . $e->getMessage() . "\n";
        }
    }
    
    // Maintenant ajoute 'sales_goal' qui dépend de 'sales'
    try {
        $db->query("ALTER TABLE products ADD COLUMN sales_goal INT DEFAULT 100 AFTER sales");
        echo "✅ Colonne 'sales_goal' ajoutée\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "⏭️  Colonne 'sales_goal' existe déjà\n";
        } else {
            echo "❌ Erreur 'sales_goal': " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✅ Migration terminée !\n\n";
    
    echo "Les colonnes ajoutées:\n";
    echo "- sales: Nombre de ventes réalisées\n";
    echo "- sales_goal: Objectif de ventes (par défaut 100)\n\n";
    
    echo "Supprime ce script:\n";
    echo "git rm public/fix-sales-columns.php\n";
    echo "git commit -m 'Remove migration script'\n";
    echo "git push origin main\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}