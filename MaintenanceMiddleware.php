<?php

namespace App\Middlewares;

class MaintenanceMiddleware
{
    public static function handle(): bool
    {
        $maintenanceMode = $_ENV['MAINTENANCE_MODE'] ?? 'false';
        
        if ($maintenanceMode === 'true' || $maintenanceMode === '1') {
            // Vérifier si l'utilisateur a le secret de bypass
            $secret = $_GET['secret'] ?? $_COOKIE['maintenance_secret'] ?? '';
            $validSecret = $_ENV['MAINTENANCE_SECRET'] ?? '';
            
            if ($secret === $validSecret && !empty($validSecret)) {
                // Définir un cookie pour ne pas avoir à fournir le secret à chaque fois
                setcookie('maintenance_secret', $secret, time() + 3600, '/', '', true, true);
                return true;
            }
            
            // Vérifier si l'utilisateur est admin
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
                return true;
            }
            
            http_response_code(503);
            require __DIR__ . '/../../views/errors/maintenance.php';
            exit;
        }
        
        return true;
    }
}
