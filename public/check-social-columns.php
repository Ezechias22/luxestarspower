<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

use App\Database;
use App\Repositories\UserRepository;

$db = Database::getInstance();
$userRepo = new UserRepository();

echo "========================================\n";
echo "🔍 VÉRIFICATION COLONNES RÉSEAUX SOCIAUX\n";
echo "========================================\n\n";

// 1. Vérifie la structure de la table users
echo "1. Structure de la table users\n";
echo "--------------------------------\n";
$columns = $db->fetchAll("DESCRIBE users");

$socialColumns = ['facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'youtube_url', 'tiktok_url', 'shop_logo', 'shop_banner'];

foreach ($socialColumns as $col) {
    $exists = false;
    foreach ($columns as $column) {
        if ($column['Field'] === $col) {
            echo "✅ $col existe (Type: {$column['Type']})\n";
            $exists = true;
            break;
        }
    }
    if (!$exists) {
        echo "❌ $col N'EXISTE PAS\n";
    }
}

echo "\n";

// 2. Vérifie les données du vendeur
echo "2. Données du vendeur (user_id = 2)\n";
echo "------------------------------------\n";
$seller = $userRepo->findById(2);

if ($seller) {
    echo "Nom: {$seller['name']}\n";
    echo "shop_name: " . ($seller['shop_name'] ?? 'NULL') . "\n";
    echo "shop_slug: " . ($seller['shop_slug'] ?? 'NULL') . "\n\n";
    
    echo "Réseaux sociaux:\n";
    echo "  facebook_url: " . ($seller['facebook_url'] ?? 'NULL') . "\n";
    echo "  twitter_url: " . ($seller['twitter_url'] ?? 'NULL') . "\n";
    echo "  instagram_url: " . ($seller['instagram_url'] ?? 'NULL') . "\n";
    echo "  linkedin_url: " . ($seller['linkedin_url'] ?? 'NULL') . "\n";
    echo "  youtube_url: " . ($seller['youtube_url'] ?? 'NULL') . "\n";
    echo "  tiktok_url: " . ($seller['tiktok_url'] ?? 'NULL') . "\n\n";
    
    echo "Images:\n";
    echo "  shop_logo: " . ($seller['shop_logo'] ?? 'NULL') . "\n";
    echo "  shop_banner: " . ($seller['shop_banner'] ?? 'NULL') . "\n";
} else {
    echo "❌ Vendeur introuvable\n";
}

echo "\n========================================\n";
echo "FIN\n";
echo "========================================\n";