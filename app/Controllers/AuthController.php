<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\CartRepository;

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    // Méthode pour routes.php: showLogin
    public function showLogin() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/login');
    }
    
    // Méthode pour routes.php: login
    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->auth->login($email, $password);
            
            // Traite les actions en attente (ajout au panier)
            $redirectUrl = $this->processPendingActions($user);
            
            // Si une redirection a été définie par processPendingActions
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
                exit;
            }
            
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
            
            // Récupère l'URL de retour
            $returnUrl = $action['return_url'] ?? '/produits';
            
            // Nettoie l'action en attente
            unset($_SESSION['pending_cart_action']);
            
            // Retourne l'URL de redirection
            return $returnUrl;
        }
        
        return null;
    }
    
    // Méthode pour routes.php: showRegister
    public function showRegister() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/register');
    }
    
    // Méthode pour routes.php: register
    public function register() {
        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (strlen($password) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
            }
            
            $user = $this->auth->register($name, $email, $password);
            $this->auth->login($email, $password);
            
            // Traite les actions en attente
            $redirectUrl = $this->processPendingActions($user);
            
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
                exit;
            }
            
            // Vérifie s'il y a une URL de redirection sauvegardée
            $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
            
            // Nettoie la variable de session
            if (isset($_SESSION['redirect_after_login'])) {
                unset($_SESSION['redirect_after_login']);
            }
            
            // Redirige vers l'URL sauvegardée ou vers le compte par défaut
            if ($redirectUrl) {
                header('Location: ' . $redirectUrl);
            } else {
                header('Location: /compte');
            }
            exit;
            
        } catch (\Exception $e) {
            view('front/auth/register', ['error' => $e->getMessage()]);
        }
    }
    
    // Méthode pour routes.php: logout
    public function logout() {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}