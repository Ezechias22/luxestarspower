<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    private $userRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
    }
    
    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);
        
        if (!$user) {
            throw new \Exception("Email ou mot de passe incorrect");
        }
        
        if (!password_verify($password, $user['password_hash'])) {
            throw new \Exception("Email ou mot de passe incorrect");
        }
        
        // Stocke les infos en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        // AJOUTER: Stocke le shop_slug pour les vendeurs
        if ($user['role'] === 'seller') {
            $_SESSION['user_shop_slug'] = $user['shop_slug'] ?? $user['store_slug'] ?? null;
        }
        
        return $user;
    }
    
    public function logout() {
        session_destroy();
        session_start();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userRepo->findById($_SESSION['user_id']);
    }
    
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /connexion');
            exit;
        }
    }
    
    public function requireRole($role) {
        $this->requireAuth();
        
        if ($_SESSION['user_role'] !== $role) {
            http_response_code(403);
            die('Accès interdit');
        }
    }
}