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
        $user = $this->auth->requireAuth();
        
        $cartItems = $this->cartRepo->getCartItems($user['id']);
        $total = $this->cartRepo->getCartTotal($user['id']);
        
        view('cart/index', [
            'user' => $user,
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }
    
    public function add() {
        $user = $this->auth->requireAuth();
        
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
        $user = $this->auth->requireAuth();
        
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
        $user = $this->auth->requireAuth();
        
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