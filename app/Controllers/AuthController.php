<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;

class AuthController {
    private $auth;
    private $userRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->userRepo = new UserRepository();
    }
    
    public function showLogin() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/login');
    }
    
    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->auth->login($email, $password);
            
            // Vérifie s'il y a une URL de redirection sauvegardée
            $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
            
            // Nettoie la variable de session
            if (isset($_SESSION['redirect_after_login'])) {
                unset($_SESSION['redirect_after_login']);
            }
            
            // Redirige vers l'URL sauvegardée ou vers le dashboard par défaut
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
            } else {
                // Redirection par défaut selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: /admin');
                } elseif ($user['role'] === 'seller') {
                    header('Location: /vendeur/tableau-de-bord');
                } else {
                    header('Location: /compte');
                }
            }
            exit;
            
        } catch (\Exception $e) {
            view('front/auth/login', ['error' => $e->getMessage()]);
        }
    }
    
    public function showRegister() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/register');
    }
    
    public function register() {
        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'buyer';
            
            // Champs boutique (si vendeur)
            $shopName = $_POST['shop_name'] ?? null;
            $shopSlug = $_POST['shop_slug'] ?? null;
            $shopDescription = $_POST['shop_description'] ?? null;
            
            // Validation
            if (strlen($password) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
            }
            
            // Si vendeur, valide les champs boutique
            if ($role === 'seller') {
                if (empty($shopName)) {
                    throw new \Exception("Le nom de la boutique est requis pour les vendeurs");
                }
                
                if (empty($shopSlug)) {
                    throw new \Exception("L'URL de la boutique est requise");
                }
                
                // Vérifie si le slug est unique
                $existingShop = $this->userRepo->findByShopSlug($shopSlug);
                
                if ($existingShop) {
                    throw new \Exception("Cette URL de boutique est déjà utilisée. Veuillez en choisir une autre.");
                }
                
                // Valide le format du slug
                if (!preg_match('/^[a-z0-9-]+$/', $shopSlug)) {
                    throw new \Exception("L'URL de la boutique ne peut contenir que des lettres minuscules, chiffres et tirets");
                }
            }
            
            // Prépare les données utilisateur
            $userData = [
                'name' => $name,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
                'role' => $role
            ];
            
            // Ajoute les infos boutique si vendeur (DOUBLE: shop_ ET store_)
            if ($role === 'seller') {
                $userData['shop_name'] = $shopName;
                $userData['shop_slug'] = $shopSlug;
                $userData['shop_description'] = $shopDescription;
                // AJOUT: store_ pour compatibilité
                $userData['store_name'] = $shopName;
                $userData['store_slug'] = $shopSlug;
                $userData['store_description'] = $shopDescription;
            }
            
            // Crée l'utilisateur via UserRepository
            $user = $this->userRepo->create($userData);
            
            if ($role === 'seller') {
                $_SESSION['flash_success'] = "Félicitations ! Votre boutique est créée : /boutique/$shopSlug";
            }
            
            // Connexion automatique
            $loggedUser = $this->auth->login($email, $password);
            
            // IMPORTANT: Stocker le shop_slug dans la session
            if ($role === 'seller') {
                $_SESSION['user_shop_slug'] = $shopSlug;
            }
            
            // Vérifie s'il y a une URL de redirection sauvegardée
            $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
            
            // Nettoie la variable de session
            if (isset($_SESSION['redirect_after_login'])) {
                unset($_SESSION['redirect_after_login']);
            }
            
            // Redirige vers l'URL sauvegardée ou vers le dashboard approprié
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
            } else {
                // Redirection selon le rôle
                if ($role === 'seller') {
                    header('Location: /vendeur/tableau-de-bord');
                } else {
                    header('Location: /compte');
                }
            }
            exit;
            
        } catch (\Exception $e) {
            view('front/auth/register', ['error' => $e->getMessage()]);
        }
    }
    
    public function logout() {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}