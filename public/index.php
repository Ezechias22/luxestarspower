<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

/**
 * LuxeStarsPower - Application Entry Point
 */

// Charger l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Charger .env seulement s'il existe (Railway utilise des variables d'environnement)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->safeLoad();
}

// Configuration des erreurs
$isProduction = ($_ENV['APP_ENV'] ?? 'development') === 'production';

if ($isProduction) {
    error_reporting(0);
    ini_set('display_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Timezone
date_default_timezone_set('UTC');

// Sessions sécurisées
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.use_strict_mode', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de sécurité
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Charger les helpers
require_once __DIR__ . '/../app/helpers.php';

// ========== SERVIR LES FICHIERS STATIQUES ==========
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Serve uploaded files (images, PDFs, ZIPs)
if (preg_match('#^/uploads/.*\.(jpg|jpeg|png|gif|webp|pdf|zip)$#i', $requestPath)) {
    $filePath = __DIR__ . $requestPath;
    
    if (file_exists($filePath) && is_file($filePath)) {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
        ];
        
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $contentType = $mimeTypes[$ext] ?? 'application/octet-stream';
        
        // Headers pour cache
        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: public, max-age=2592000'); // 30 jours
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 2592000) . ' GMT');
        
        // Lecture du fichier
        readfile($filePath);
        exit;
    }
    
    // Fichier introuvable
    http_response_code(404);
    if (!$isProduction) {
        echo "File not found: " . htmlspecialchars($requestPath);
    }
    exit;
}
// ========== FIN SERVIR LES FICHIERS STATIQUES ==========

// ========== CONNEXION BASE DE DONNÉES ==========
$dbHost = $_ENV['DB_HOST'] ?? null;
$dbName = $_ENV['DB_NAME'] ?? null; 
$dbUser = $_ENV['DB_USER'] ?? null;
$dbPass = $_ENV['DB_PASS'] ?? null;

if (!$dbHost || !$dbName || !$dbUser) {
    die("Database configuration missing!");
}

try {
    $db = new PDO(
        "mysql:host=$dbHost;port=3306;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    $GLOBALS['db'] = $db;
} catch (PDOException $e) {
    if ($isProduction) {
        die("Database connection failed.");
    } else {
        die("Database error: " . $e->getMessage());
    }
}
// ========== FIN CONNEXION DB ==========

// ========== INITIALISER I18N ==========
\App\I18n::init(); // Initialise automatiquement la locale
// ========== FIN I18N ==========

// Initialiser le routeur
$router = new \App\Router();

// Charger les routes
require_once __DIR__ . '/../routes.php';

// Exécuter le routeur
try {
    $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
} catch (Exception $e) {
    if ($isProduction) {
        http_response_code(500);
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    } else {
        echo "<h1>Erreur</h1>";
        echo "<pre>";
        echo "Message: " . $e->getMessage() . "\n";
        echo "Fichier: " . $e->getFile() . "\n";
        echo "Ligne: " . $e->getLine() . "\n\n";
        echo "Stack trace:\n" . $e->getTraceAsString();
        echo "</pre>";
    }
    exit(1);
}