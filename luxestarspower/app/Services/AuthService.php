<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    private $userRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
    }
    
    public function register($name, $email, $password, $role = 'buyer') {
        if ($this->userRepo->findByEmail($email)) {
            throw new \Exception("Email already exists");
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
        if (!$user || !password_verify($password, $user->password_hash)) {
            throw new \Exception("Invalid credentials");
        }
        
        if (!$user->is_active) {
            throw new \Exception("Account is inactive");
        }
        
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;
        $this->logActivity($user->id, 'login');
        
        return $user;
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout');
        }
        session_destroy();
    }
    
    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) return null;
        return $this->userRepo->findById($_SESSION['user_id']);
    }
    
    public function requireAuth() {
        $user = $this->getCurrentUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }
        return $user;
    }
    
    public function requireRole($role) {
        $user = $this->requireAuth();
        if ($user->role !== $role && $user->role !== 'admin') {
            http_response_code(403);
            die('Forbidden');
        }
        return $user;
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    private function logActivity($userId, $action) {
        $db = \App\Database::getInstance();
        $db->insert(
            "INSERT INTO activity_logs (user_id, action_type, ip_address, created_at) VALUES (?, ?, ?, NOW())",
            [$userId, $action, $_SERVER['REMOTE_ADDR'] ?? '']
        );
    }
    
    public function generatePasswordResetToken($email) {
        $user = $this->userRepo->findByEmail($email);
        if (!$user) return null;
        
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $db = \App\Database::getInstance();
        $db->query("DELETE FROM password_resets WHERE user_id = ?", [$user->id]);
        $db->insert("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)", [$user->id, $token, $expiry]);
        
        return $token;
    }
}
