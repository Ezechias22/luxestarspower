<?php
/**
 * Migration accessible via URL pour Railway
 * URL: https://luxestarspower.com/migrate.php?secret=ton_secret_ici
 */

// Sécurité : vérifie le secret
$SECRET_KEY = 'luxestar2025migration'; // Change ce secret !

if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET_KEY) {
    http_response_code(403);
    die('Accès refusé');
}

// Charge l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

header('Content-Type: text/plain; charset=utf-8');

echo "╔════════════════════════════════════════════════════════╗\n";
echo "║          🚀 LUXE STARS POWER - MIGRATION 🚀           ║\n";
echo "╚════════════════════════════════════════════════════════╝\n\n";

try {
    echo "✅ Connexion à la base de données...\n";
    $db = Database::getInstance();
    echo "✅ Connexion établie\n\n";
    
    // Lit le fichier SQL
    $sql = file_get_contents(__DIR__ . '/../database/migrations/add_store_columns.sql');
    
    // Sépare les requêtes
    $queries = [];
    $currentQuery = '';
    $lines = explode("\n", $sql);
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '--') === 0) {
            continue;
        }
        $currentQuery .= $line . ' ';
        if (substr($line, -1) === ';') {
            $queries[] = trim($currentQuery);
            $currentQuery = '';
        }
    }
    
    echo "📊 Nombre de requêtes : " . count($queries) . "\n\n";
    
    $success = 0;
    $failed = 0;
    $queryNumber = 1;
    
    foreach ($queries as $query) {
        if (empty(trim($query))) continue;
        
        echo "[$queryNumber/" . count($queries) . "] ";
        $preview = substr($query, 0, 60);
        if (strlen($query) > 60) $preview .= '...';
        echo $preview . "\n";
        
        try {
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $success++;
            echo "   ✅ Succès\n";
            
            // Affiche les résultats pour les SELECT
            if (stripos($query, 'SELECT') === 0) {
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($results)) {
                    foreach ($results[0] as $key => $value) {
                        echo "   → $key: $value\n";
                    }
                }
            }
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "   ⚠️  Déjà appliquée\n";
            } else {
                $failed++;
                echo "   ❌ Erreur: " . $e->getMessage() . "\n";
            }
        }
        
        $queryNumber++;
        echo "\n";
    }
    
    echo "\n═══ RÉSUMÉ ═══\n";
    echo "✅ Succès : $success\n";
    echo "❌ Échecs : $failed\n";
    
    if ($failed === 0) {
        echo "\n🎉 MIGRATION TERMINÉE AVEC SUCCÈS !\n";
    }
    
    // Vérifie les vendeurs
    echo "\n═══ VÉRIFICATION ═══\n";
    $sellers = $db->fetchAll(
        "SELECT id, name, shop_slug, store_slug FROM users WHERE role = 'seller' LIMIT 5"
    );
    
    if (!empty($sellers)) {
        echo "Vendeurs trouvés : " . count($sellers) . "\n";
        foreach ($sellers as $seller) {
            echo "  • {$seller['name']} - shop: {$seller['shop_slug']} - store: {$seller['store_slug']}\n";
        }
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERREUR FATALE : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}