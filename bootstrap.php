<?php

// Chargement de l'autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Chargement des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configuration des erreurs selon l'environnement
if ($_ENV['APP_ENV'] === 'production') {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
}

// Configuration de la timezone
date_default_timezone_set('UTC');

// Configuration des sessions sécurisées
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_name('LUXESP_SESSION');

// Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Headers de sécurité
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");

if ($_ENV['APP_ENV'] === 'production') {
    header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
}

// Content Security Policy
$csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' https://js.stripe.com https://www.paypal.com",
    "style-src 'self' 'unsafe-inline'",
    "img-src 'self' data: https: " . ($_ENV['CDN_URL'] ?? ''),
    "font-src 'self' data:",
    "connect-src 'self' https://api.stripe.com",
    "frame-src https://js.stripe.com https://www.paypal.com",
    "media-src 'self' " . ($_ENV['CDN_URL'] ?? ''),
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'none'"
];
header("Content-Security-Policy: " . implode('; ', $csp));

// Initialisation du logger
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;

$logger = new Logger('luxestarspower');

if ($_ENV['APP_ENV'] === 'production') {
    $logger->pushHandler(new RotatingFileHandler(
        __DIR__ . '/../storage/logs/app.log',
        7,
        Logger::ERROR
    ));
} else {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/../storage/logs/app.log',
        Logger::DEBUG
    ));
}

// Gestionnaire d'erreurs global
set_error_handler(function ($severity, $message, $file, $line) use ($logger) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $logger->error('PHP Error', [
        'message' => $message,
        'file' => $file,
        'line' => $line,
        'severity' => $severity
    ]);
    
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Gestionnaire d'exceptions global
set_exception_handler(function ($exception) use ($logger) {
    $logger->critical('Uncaught Exception', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    if ($_ENV['APP_ENV'] === 'production') {
        http_response_code(500);
        require __DIR__ . '/../views/errors/500.php';
    } else {
        echo '<pre>';
        echo "Exception: " . $exception->getMessage() . "\n";
        echo "File: " . $exception->getFile() . "\n";
        echo "Line: " . $exception->getLine() . "\n";
        echo "\nStack Trace:\n" . $exception->getTraceAsString();
        echo '</pre>';
    }
    exit(1);
});

// Enregistrement du logger dans le container global
$GLOBALS['logger'] = $logger;

return $logger;
