<?php

namespace App\Middlewares;

class AdminMiddleware
{
    public static function handle(): bool
    {
        // Vérifier que l'utilisateur est connecté
        if (!AuthMiddleware::check()) {
            header('Location: /connexion');
            exit;
        }
        
        // Vérifier que l'utilisateur a le rôle admin
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }
        
        return true;
    }
}
