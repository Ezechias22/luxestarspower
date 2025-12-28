<?php
require_once __DIR__ . '/../vendor/autoload.php';

$SECRET = 'luxestar2025';
if (!isset($_GET['secret']) || $_GET['secret'] !== $SECRET) {
    die('Accès refusé');
}

header('Content-Type: text/plain; charset=utf-8');

echo "========================================\n";
echo "🔍 TEST BUNNYCDN UPLOAD\n";
echo "========================================\n\n";

// Charge les variables d'environnement
$bunnyStorageZone = $_ENV['BUNNY_STORAGE_ZONE'] ?? '';
$bunnyAccessKey = $_ENV['BUNNY_ACCESS_KEY'] ?? '';
$bunnyHostname = $_ENV['BUNNY_HOSTNAME'] ?? '';

echo "Configuration BunnyCDN:\n";
echo "----------------------\n";
echo "BUNNY_STORAGE_ZONE: " . ($bunnyStorageZone ?: '❌ NON DÉFINI') . "\n";
echo "BUNNY_ACCESS_KEY: " . ($bunnyAccessKey ? '✅ Défini (' . strlen($bunnyAccessKey) . ' caractères)' : '❌ NON DÉFINI') . "\n";
echo "BUNNY_HOSTNAME: " . ($bunnyHostname ?: '❌ NON DÉFINI') . "\n\n";

if (empty($bunnyStorageZone) || empty($bunnyAccessKey)) {
    echo "❌ ERREUR: Configuration BunnyCDN incomplète\n\n";
    echo "Ajoute ces variables dans ton .env sur Railway:\n";
    echo "BUNNY_STORAGE_ZONE=luxestarspower\n";
    echo "BUNNY_ACCESS_KEY=ta-clé-api-bunny\n";
    echo "BUNNY_HOSTNAME=luxestarspower.b-cdn.net\n";
    exit;
}

// Test d'upload
echo "Test d'upload vers BunnyCDN...\n";
echo "--------------------------------\n";

$testContent = "Test upload " . date('Y-m-d H:i:s');
$testFilename = 'test-' . time() . '.txt';
$bunnyPath = '/shop-images/' . $testFilename;

echo "Fichier: $testFilename\n";
echo "Chemin: $bunnyPath\n";
echo "URL complète: https://storage.bunnycdn.com/{$bunnyStorageZone}{$bunnyPath}\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://storage.bunnycdn.com/{$bunnyStorageZone}{$bunnyPath}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
curl_setopt($ch, CURLOPT_POSTFIELDS, $testContent);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'AccessKey: ' . $bunnyAccessKey,
    'Content-Type: application/octet-stream'
]);

echo "Envoi de la requête...\n";
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "Code HTTP: $httpCode\n";
echo "Réponse: " . ($response ?: 'vide') . "\n";

if ($curlError) {
    echo "Erreur CURL: $curlError\n";
}

echo "\n";

if ($httpCode === 201) {
    echo "✅ UPLOAD RÉUSSI !\n";
    echo "URL publique: https://{$bunnyHostname}{$bunnyPath}\n";
} else {
    echo "❌ UPLOAD ÉCHOUÉ\n\n";
    
    echo "Causes possibles:\n";
    echo "1. AccessKey incorrecte\n";
    echo "2. Storage Zone incorrecte\n";
    echo "3. Permissions insuffisantes\n";
    echo "4. Le dossier /shop-images/ n'existe pas\n\n";
    
    echo "Solutions:\n";
    echo "1. Vérifie ton AccessKey dans BunnyCDN Dashboard\n";
    echo "2. Vérifie le nom de ta Storage Zone\n";
    echo "3. Crée le dossier /shop-images/ dans BunnyCDN\n";
}

echo "\n========================================\n";
echo "FIN DU TEST\n";
echo "========================================\n";