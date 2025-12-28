<?php
$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

$file = __DIR__ . '/../app/Controllers/ShopController.php';

if (!file_exists($file)) {
    die("❌ Fichier introuvable: $file");
}

echo "========================================\n";
echo "📄 CONTENU DE ShopController.php\n";
echo "========================================\n\n";

$content = file_get_contents($file);

// Trouve la ligne avec la requête products
preg_match('/\/\/ Récupère les produits actifs.*?ORDER BY created_at DESC.*?LIMIT 50.*?\);/s', $content, $matches);

if ($matches) {
    echo "🔍 Requête SQL pour récupérer les produits:\n";
    echo "==========================================\n";
    echo $matches[0];
    echo "\n==========================================\n\n";
} else {
    echo "⚠️ Requête SQL non trouvée dans le pattern attendu\n\n";
    echo "Recherche de 'SELECT * FROM products'...\n";
    
    if (preg_match('/SELECT \* FROM products.*?;/s', $content, $matches2)) {
        echo $matches2[0];
    } else {
        echo "❌ Aucune requête SELECT trouvée\n";
    }
}

// Montre les lignes 40-60
echo "\n\n🔍 Lignes 40-60 de ShopController.php:\n";
echo "==========================================\n";
$lines = explode("\n", $content);
for ($i = 39; $i < 60 && $i < count($lines); $i++) {
    printf("%3d: %s\n", $i + 1, $lines[$i]);
}

echo "\n========================================\n";
echo "FIN\n";
echo "========================================\n";