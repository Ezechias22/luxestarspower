<?php
namespace App\Middlewares;

class CsrfMiddleware {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function verifyRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!self::validateToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }
}

function csrf_field() {
    $token = \App\Middlewares\CsrfMiddleware::generateToken();
    return '<input type="hidden" name="_token" value="' . htmlspecialchars($token) . '">';
}

function csrf_token() {
    return \App\Middlewares\CsrfMiddleware::generateToken();
}
