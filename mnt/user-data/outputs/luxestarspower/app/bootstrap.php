<?php

// Error reporting
if (env('APP_DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Timezone
date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', env('SESSION_SECURE', 1));
ini_set('session.cookie_samesite', env('SESSION_SAME_SITE', 'Strict'));
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', env('SESSION_LIFETIME', 7200));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clean up flash data
if (isset($_SESSION['_flash_messages'])) {
    unset($_SESSION['_flash_messages']);
}

// Set locale from session or default
$locale = $_SESSION['locale'] ?? config('app.locale', 'fr');
setlocale(LC_TIME, $locale . '_' . strtoupper($locale) . '.UTF-8');

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

if (!env('APP_DEBUG', false)) {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://js.stripe.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' data:; connect-src 'self' https://api.stripe.com;");
}

// HTTPS redirect in production
if (env('APP_ENV') === 'production' && (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect, true, 301);
    exit;
}

// Maintenance mode check
if (config('app.maintenance.enabled') && !in_array($_SERVER['REMOTE_ADDR'] ?? '', config('app.maintenance.allowed_ips', []))) {
    $secret = $_GET['secret'] ?? '';
    if ($secret !== config('app.maintenance.secret')) {
        http_response_code(503);
        if (file_exists(__DIR__ . '/../views/errors/503.php')) {
            require __DIR__ . '/../views/errors/503.php';
        } else {
            die('Site under maintenance. Please try again later.');
        }
        exit;
    }
}

// Database connection
global $db;
$db = null;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        config('database.connections.mysql.host'),
        config('database.connections.mysql.port'),
        config('database.connections.mysql.database'),
        config('database.connections.mysql.charset')
    );
    
    $db = new PDO(
        $dsn,
        config('database.connections.mysql.username'),
        config('database.connections.mysql.password'),
        config('database.connections.mysql.options')
    );
} catch (PDOException $e) {
    if (env('APP_DEBUG', false)) {
        die('Database connection failed: ' . $e->getMessage());
    }
    logger()->error('Database connection failed', ['error' => $e->getMessage()]);
    http_response_code(500);
    die('Service temporarily unavailable');
}

// Error handler
set_exception_handler(function ($exception) {
    logger()->error('Uncaught exception', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    if (env('APP_DEBUG', false)) {
        echo '<h1>Error</h1>';
        echo '<p>' . $exception->getMessage() . '</p>';
        echo '<pre>' . $exception->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        if (file_exists(__DIR__ . '/../views/errors/500.php')) {
            require __DIR__ . '/../views/errors/500.php';
        } else {
            die('An error occurred. Please try again later.');
        }
    }
});

// Load routes
global $router;
$router = new \App\Router();

require __DIR__ . '/routes.php';
