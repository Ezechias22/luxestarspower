<?php

namespace App\Middlewares;

class Seller
{
    public function handle()
    {
        $user = $_SESSION['user'] ?? null;
        
        if (!$user || !in_array($user['role'], ['seller', 'admin'])) {
            flash('error', __('errors.seller_only'));
            redirect(route('seller.onboarding'));
            return false;
        }
        
        return true;
    }
}
