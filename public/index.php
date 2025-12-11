<?php
echo "<h1>✅ LuxeStarsPower est en ligne !</h1>";
echo "<p>Le serveur fonctionne correctement.</p>";
echo "<hr>";
echo "<h2>Informations système :</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Database: ";
try {
    $pdo = new PDO(
        "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
        getenv('DB_USER'),
        getenv('DB_PASS')
    );
    echo "✅ Connecté à MySQL\n";
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
echo "</pre>";