<?php

namespace App\Middlewares;

class Auth
{
    public function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            flash('error', __('auth.please_login'));
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            redirect(route('login'));
            return false;
        }
        
        // Load user data
        global $db;
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            session_destroy();
            flash('error', __('auth.account_not_found'));
            redirect(route('login'));
            return false;
        }
        
        $_SESSION['user'] = $user;
        return true;
    }
}
