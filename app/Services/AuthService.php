<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    private $userRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
    }
    
    public function register($name, $email, $password, $role = 'buyer') {
        $existing = $this->userRepo->findByEmail($email);
        if ($existing) {
            throw new \Exception("Un compte existe déjà avec cet email");
        }
        
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);
        
        return $this->userRepo->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => $role
        ]);
    }
    
    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);
        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new \Exception("Email ou mot de passe incorrect");
        }
        
        if (isset($user['is_active']) && !$user['is_active']) {
            throw new \Exception("Ce compte est désactivé");
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        return $user;
    }
    
    public function logout() {
        session_destroy();
        session_start();
    }
    
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        return $this->userRepo->findById($_SESSION['user_id']);
    }
    
    public function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $this->getCurrentUser();
        if (!$user) {
            header('Location: /connexion');
            exit;
        }
        
        return $user;
    }
    
    public function requireRole($role) {
        $user = $this->requireAuth();
        
        if ($user['role'] !== $role && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        return $user;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}