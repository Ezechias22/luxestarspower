<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    // Lit le fichier SQL
    $sql = file_get_contents(__DIR__ . '/../migrations/add_subscriptions.sql');
    
    // Enlève les commentaires et split par requête
    $sql = preg_replace('/--.*$/m', '', $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    
    // Exécute chaque requête
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    $success = 0;
    $errors = 0;
    
    foreach ($queries as $query) {
        if (empty($query)) continue;
        
        try {
            // Skip les DELIMITER et CREATE PROCEDURE pour l'instant
            if (stripos($query, 'DELIMITER') !== false || 
                stripos($query, 'CREATE PROCEDURE') !== false ||
                stripos($query, 'CREATE OR REPLACE VIEW') !== false) {
                continue;
            }
            
            $db->query($query);
            $success++;
        } catch (Exception $e) {
            echo "❌ Erreur: " . $e->getMessage() . "\n";
            echo "Requête: " . substr($query, 0, 100) . "...\n\n";
            $errors++;
        }
    }
    
    echo "✅ Migration terminée !\n";
    echo "✓ Requêtes réussies: $success\n";
    echo "✗ Erreurs: $errors\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR CRITIQUE: " . $e->getMessage();
}