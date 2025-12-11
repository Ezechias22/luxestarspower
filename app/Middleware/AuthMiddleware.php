<?php

namespace App\Middlewares;

class AuthMiddleware
{
    public static function handle(): bool
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_email'])) {
            // Sauvegarder l'URL demandée pour redirection après login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            header('Location: /connexion');
            exit;
        }
        
        // Vérifier la validité de la session (timeout)
        $sessionLifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 120) * 60;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
            session_unset();
            session_destroy();
            header('Location: /connexion?timeout=1');
            exit;
        }
        
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    public static function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'] ?? 'buyer',
            'avatar_url' => $_SESSION['user_avatar'] ?? null,
        ];
    }
    
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
