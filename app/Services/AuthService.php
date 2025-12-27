<?php
namespace App\Services;

use App\Repositories\UserRepository;

class AuthService {
    private $userRepo;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
    }
    
    /**
     * Connecte un utilisateur avec email et mot de passe
     */
    public function login($email, $password) {
        $user = $this->userRepo->findByEmail($email);
        
        if (!$user) {
            throw new \Exception("Email ou mot de passe incorrect");
        }
        
        // Vérifie le mot de passe
        if (!password_verify($password, $user['password_hash'])) {
            throw new \Exception("Email ou mot de passe incorrect");
        }
        
        // Vérifie si le compte est actif
        if (isset($user['is_suspended']) && $user['is_suspended'] == 1) {
            throw new \Exception("Votre compte a été suspendu. Contactez le support.");
        }
        
        // Stocke les informations en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        
        // IMPORTANT: Stocke le shop_slug pour les vendeurs
        if ($user['role'] === 'seller') {
            $_SESSION['user_shop_slug'] = $user['shop_slug'] ?? $user['store_slug'] ?? null;
            $_SESSION['user_shop_name'] = $user['shop_name'] ?? $user['store_name'] ?? null;
        }
        
        // Met à jour la date de dernière connexion
        $this->userRepo->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);
        
        return $user;
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public function logout() {
        // Sauvegarde la langue si elle existe
        $language = $_SESSION['language'] ?? null;
        
        // Détruit la session
        session_unset();
        session_destroy();
        
        // Redémarre une nouvelle session
        session_start();
        
        // Restaure la langue
        if ($language) {
            $_SESSION['language'] = $language;
        }
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Récupère l'utilisateur actuellement connecté
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        // Si l'utilisateur n'existe plus en BDD, déconnecte
        if (!$user) {
            $this->logout();
            return null;
        }
        
        return $user;
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     */
    public function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public function hasRole($role) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return ($_SESSION['user_role'] ?? '') === $role;
    }
    
    /**
     * Vérifie si l'utilisateur est un admin
     */
    public function isAdmin() {
        return $this->hasRole('admin');
    }
    
    /**
     * Vérifie si l'utilisateur est un vendeur
     */
    public function isSeller() {
        return $this->hasRole('seller');
    }
    
    /**
     * Vérifie si l'utilisateur est un acheteur
     */
    public function isBuyer() {
        return $this->hasRole('buyer');
    }
    
    /**
     * Requiert une authentification et RETOURNE l'utilisateur
     * CORRECTION CRITIQUE: Doit retourner l'utilisateur !
     */
    public function requireAuth() {
        if (!$this->isLoggedIn()) {
            // Sauvegarde l'URL actuelle pour redirection après login
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '/';
            
            // Redirige vers la page de connexion
            header('Location: /connexion');
            exit;
        }
        
        // CORRECTION: Récupère et retourne l'utilisateur
        $user = $this->getCurrentUser();
        
        if (!$user) {
            // Si l'utilisateur n'existe plus, déconnecte
            $this->logout();
            header('Location: /connexion');
            exit;
        }
        
        return $user;
    }
    
    /**
     * Requiert un rôle spécifique et retourne l'utilisateur
     */
    public function requireRole($role) {
        $user = $this->requireAuth();
        
        if ($user['role'] !== $role && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit. Vous n\'avez pas les permissions nécessaires.');
        }
        
        return $user;
    }
    
    /**
     * Requiert le rôle admin
     */
    public function requireAdmin() {
        return $this->requireRole('admin');
    }
    
    /**
     * Requiert le rôle vendeur
     */
    public function requireSeller() {
        $user = $this->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            $_SESSION['error'] = 'Accès réservé aux vendeurs';
            header('Location: /vendre');
            exit;
        }
        
        return $user;
    }
    
    /**
     * Vérifie si l'utilisateur connecté est propriétaire de la ressource
     */
    public function isOwner($ownerId) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        return $this->getCurrentUserId() == $ownerId;
    }
    
    /**
     * Requiert que l'utilisateur soit propriétaire de la ressource ou admin
     */
    public function requireOwnerOrAdmin($ownerId) {
        $user = $this->requireAuth();
        
        if (!$this->isOwner($ownerId) && !$this->isAdmin()) {
            http_response_code(403);
            die('Accès interdit. Vous n\'êtes pas autorisé à accéder à cette ressource.');
        }
        
        return $user;
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function register($data) {
        // Validation de base
        if (empty($data['email']) || empty($data['password'])) {
            throw new \Exception("Email et mot de passe requis");
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email invalide");
        }
        
        if (strlen($data['password']) < 8) {
            throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
        }
        
        // Vérifie si l'email existe déjà
        $existingUser = $this->userRepo->findByEmail($data['email']);
        if ($existingUser) {
            throw new \Exception("Cet email est déjà utilisé");
        }
        
        // Hash le mot de passe
        $data['password_hash'] = password_hash($data['password'], PASSWORD_ARGON2ID);
        unset($data['password']);
        
        // Crée l'utilisateur
        $user = $this->userRepo->create($data);
        
        return $user;
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     */
    public function changePassword($userId, $currentPassword, $newPassword) {
        $user = $this->userRepo->findById($userId);
        
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        
        // Vérifie l'ancien mot de passe
        if (!password_verify($currentPassword, $user['password_hash'])) {
            throw new \Exception("Mot de passe actuel incorrect");
        }
        
        // Valide le nouveau mot de passe
        if (strlen($newPassword) < 8) {
            throw new \Exception("Le nouveau mot de passe doit contenir au moins 8 caractères");
        }
        
        // Hash et met à jour
        $newPasswordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        
        return $this->userRepo->update($userId, [
            'password_hash' => $newPasswordHash
        ]);
    }
    
    /**
     * Génère un token de réinitialisation de mot de passe
     */
    public function generatePasswordResetToken($email) {
        $user = $this->userRepo->findByEmail($email);
        
        if (!$user) {
            // Pour la sécurité, ne pas révéler si l'email existe ou non
            return true;
        }
        
        // Génère un token aléatoire
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Sauvegarde le token
        $this->userRepo->update($user['id'], [
            'password_reset_token' => $token,
            'password_reset_expires' => $expires
        ]);
        
        return $token;
    }
    
    /**
     * Réinitialise le mot de passe avec un token
     */
    public function resetPasswordWithToken($token, $newPassword) {
        if (strlen($newPassword) < 8) {
            throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
        }
        
        // Trouve l'utilisateur avec ce token valide
        $user = $this->userRepo->findByPasswordResetToken($token);
        
        if (!$user) {
            throw new \Exception("Token invalide ou expiré");
        }
        
        // Vérifie l'expiration
        if (strtotime($user['password_reset_expires']) < time()) {
            throw new \Exception("Token expiré");
        }
        
        // Hash le nouveau mot de passe
        $passwordHash = password_hash($newPassword, PASSWORD_ARGON2ID);
        
        // Met à jour et supprime le token
        $this->userRepo->update($user['id'], [
            'password_hash' => $passwordHash,
            'password_reset_token' => null,
            'password_reset_expires' => null
        ]);
        
        return true;
    }
    
    /**
     * Vérifie si un email est déjà utilisé
     */
    public function emailExists($email) {
        return $this->userRepo->findByEmail($email) !== null;
    }
    
    /**
     * Vérifie si un shop_slug est déjà utilisé
     */
    public function shopSlugExists($slug) {
        return $this->userRepo->findByShopSlug($slug) !== null;
    }
}