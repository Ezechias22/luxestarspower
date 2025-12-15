<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\CartRepository;

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

        // Stocke TOUTES les infos utilisateur
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user'] = $user; // ← AJOUT CRITIQUE

        // Traite les actions en attente (ajout au panier)
        $this->processPendingActions($user);

        return $user;
    }

    // Traite les actions en attente après connexion
    private function processPendingActions($user) {
        // Vérifie s'il y a une action de panier en attente
        if (isset($_SESSION['pending_cart_action'])) {
            $action = $_SESSION['pending_cart_action'];
            
            // Ajoute le produit au panier
            if ($action['action'] === 'add' && $action['product_id']) {
                try {
                    $cartRepo = new CartRepository();
                    $cartRepo->addToCart($user['id'], $action['product_id'], $action['quantity']);
                    $_SESSION['flash_success'] = 'Produit ajouté au panier !';
                } catch (\Exception $e) {
                    $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
                }
            }
            
            // Nettoie l'action en attente
            unset($_SESSION['pending_cart_action']);
        }
    }

    public function logout() {
        session_destroy();
        session_start();
    }

    public function getCurrentUser() {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        // Retourne depuis la session si disponible
        if (isset($_SESSION['user'])) {
            return $_SESSION['user'];
        }

        // Sinon charge depuis la DB
        $user = $this->userRepo->findById($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user'] = $user;
        }
        return $user;
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