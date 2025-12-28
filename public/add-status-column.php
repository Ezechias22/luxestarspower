<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "🔧 AJOUT DE LA COLONNE status\n";
echo "========================================\n\n";

use App\Database;

try {
    $db = Database::getInstance();
    
    // Vérifie si la colonne existe déjà
    echo "1. Vérification de l'existence de la colonne...\n";
    
    $columns = $db->fetchAll("DESCRIBE products");
    $statusExists = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'status') {
            $statusExists = true;
            break;
        }
    }
    
    if ($statusExists) {
        echo "✅ La colonne 'status' existe déjà !\n\n";
        
        // Affiche les infos
        foreach ($columns as $col) {
            if ($col['Field'] === 'status') {
                echo "Détails de la colonne:\n";
                echo "  Type: {$col['Type']}\n";
                echo "  Null: {$col['Null']}\n";
                echo "  Default: {$col['Default']}\n";
                echo "  Extra: {$col['Extra']}\n";
                break;
            }
        }
    } else {
        echo "⚠️ La colonne 'status' n'existe pas.\n\n";
        
        echo "2. Ajout de la colonne 'status'...\n";
        
        $sql = "ALTER TABLE products 
                ADD COLUMN status VARCHAR(20) DEFAULT NULL 
                AFTER is_active";
        
        $db->query($sql);
        
        echo "✅ Colonne 'status' ajoutée avec succès !\n\n";
        
        // Vérifie
        echo "3. Vérification...\n";
        $columns = $db->fetchAll("DESCRIBE products");
        
        foreach ($columns as $col) {
            if ($col['Field'] === 'status') {
                echo "✅ Colonne confirmée:\n";
                echo "  Type: {$col['Type']}\n";
                echo "  Null: {$col['Null']}\n";
                echo "  Default: {$col['Default']}\n";
                break;
            }
        }
    }
    
    echo "\n";
    echo "4. Ajout d'un index pour optimiser les requêtes...\n";
    
    try {
        $db->query("ALTER TABLE products ADD INDEX idx_status (status)");
        echo "✅ Index ajouté sur la colonne 'status'\n";
    } catch (\Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo "✅ Index existe déjà\n";
        } else {
            echo "⚠️ Erreur lors de l'ajout de l'index: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    echo "5. Statistiques des produits par status...\n";
    
    $stats = $db->fetchAll("
        SELECT 
            status, 
            COUNT(*) as count 
        FROM products 
        GROUP BY status
    ");
    
    echo "┌─────────────────────┬───────┐\n";
    echo "│ Status              │ Count │\n";
    echo "├─────────────────────┼───────┤\n";
    
    foreach ($stats as $stat) {
        $status = $stat['status'] ?? 'NULL';
        printf("│ %-19s │ %5d │\n", $status, $stat['count']);
    }
    
    echo "└─────────────────────┴───────┘\n";
    
    echo "\n========================================\n";
    echo "✅ TERMINÉ AVEC SUCCÈS !\n";
    echo "========================================\n\n";
    
    echo "ℹ️ Valeurs possibles pour 'status':\n";
    echo "  - NULL         : Pas encore vérifié (défaut)\n";
    echo "  - 'pending'    : En attente de validation\n";
    echo "  - 'approved'   : Approuvé par admin\n";
    echo "  - 'rejected'   : Rejeté par admin\n\n";
    
    echo "🗑️ Pour supprimer ce script après utilisation:\n";
    echo "rm public/add-status-column.php\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "FIN\n";
echo "========================================\n";