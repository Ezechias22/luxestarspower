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
    
    echo "Ajout des colonnes pour les réseaux sociaux...\n\n";
    
    $columns = [
        'facebook_url' => "ALTER TABLE users ADD COLUMN facebook_url VARCHAR(255) DEFAULT NULL AFTER shop_description",
        'twitter_url' => "ALTER TABLE users ADD COLUMN twitter_url VARCHAR(255) DEFAULT NULL AFTER facebook_url",
        'instagram_url' => "ALTER TABLE users ADD COLUMN instagram_url VARCHAR(255) DEFAULT NULL AFTER twitter_url",
        'linkedin_url' => "ALTER TABLE users ADD COLUMN linkedin_url VARCHAR(255) DEFAULT NULL AFTER instagram_url",
        'youtube_url' => "ALTER TABLE users ADD COLUMN youtube_url VARCHAR(255) DEFAULT NULL AFTER linkedin_url",
        'tiktok_url' => "ALTER TABLE users ADD COLUMN tiktok_url VARCHAR(255) DEFAULT NULL AFTER youtube_url",
    ];
    
    foreach ($columns as $col => $sql) {
        try {
            $db->query($sql);
            echo "✅ Colonne $col ajoutée\n";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⏭️  Colonne $col existe déjà\n";
            } else {
                echo "❌ Erreur $col: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✅ Migration terminée !\n";
    echo "\nSupprime ce script:\nrm public/add-social-links.php\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
}