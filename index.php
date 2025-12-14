<?php

// Front Controller - Single entry point for all requests

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Bootstrap application
require_once __DIR__ . '/../app/bootstrap.php';

// Dispatch request
global $router;
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
