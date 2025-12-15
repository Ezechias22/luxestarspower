<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\CartRepository;

class CartController {
    private $auth;
    private $cartRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->cartRepo = new CartRepository();
    }
    
    public function index() {
        // Sauvegarde l'URL pour redirection après login
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = '/panier';
            header('Location: /connexion');
            exit;
        }
        
        $user = $_SESSION['user'];
        
        $cartItems = $this->cartRepo->getCartItems($user['id']);
        $total = $this->cartRepo->getCartTotal($user['id']);
        
        view('cart/index', [
            'user' => $user,
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }
    
    public function add() {
        // Si pas connecté, sauvegarde l'action et redirige vers login
        if (!isset($_SESSION['user'])) {
            $_SESSION['pending_cart_action'] = [
                'action' => 'add',
                'product_id' => $_POST['product_id'] ?? null,
                'quantity' => $_POST['quantity'] ?? 1,
                'return_url' => $_SERVER['HTTP_REFERER'] ?? '/produits'
            ];
            header('Location: /connexion');
            exit;
        }
        
        $user = $_SESSION['user'];
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$productId) {
            $_SESSION['flash_error'] = 'Produit invalide';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/produits'));
            exit;
        }
        
        try {
            $this->cartRepo->addToCart($user['id'], $productId, $quantity);
            $_SESSION['flash_success'] = 'Produit ajouté au panier !';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/produits'));
        exit;
    }
    
    public function remove($productId) {
        if (!isset($_SESSION['user'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $_SESSION['user'];
        
        try {
            $this->cartRepo->removeFromCart($user['id'], $productId);
            $_SESSION['flash_success'] = 'Produit retiré du panier';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: /panier');
        exit;
    }
    
    public function updateQuantity() {
        if (!isset($_SESSION['user'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $_SESSION['user'];
        
        $productId = $_POST['product_id'] ?? null;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$productId) {
            header('Location: /panier');
            exit;
        }
        
        try {
            $this->cartRepo->updateQuantity($user['id'], $productId, $quantity);
            $_SESSION['flash_success'] = 'Quantité mise à jour';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: /panier');
        exit;
    }
}