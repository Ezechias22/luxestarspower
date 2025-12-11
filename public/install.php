<?php
echo "<h1>Installation de LuxeStarsPower</h1>";
echo "<pre>";

// Connexion DB
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;port=3306;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion MySQL réussie\n\n";
    
    // Créer la base si nécessaire
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    echo "✅ Base de données '$dbname' sélectionnée\n\n";
    
    // Charger et exécuter la migration
    require_once __DIR__ . '/../001_create_all_tables.php';
    
    $migration = new Migration_001_CreateAllTables($pdo);
    $migration->up();
    
    echo "\n🎉 Installation terminée avec succès !\n";
    echo "Toutes les tables ont été créées.\n\n";
    
    // Afficher les tables créées
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables créées (" . count($tables) . ") :\n";
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString();
    exit(1);
}

echo "</pre>";
echo '<p><a href="/">← Retour à l\'accueil</a></p>';