<?php

namespace App\Middlewares;

class Admin
{
    public function handle()
    {
        $user = $_SESSION['user'] ?? null;
        
        if (!$user || $user['role'] !== 'admin') {
            abort(403, __('errors.admin_only'));
            return false;
        }
        
        return true;
    }
}
