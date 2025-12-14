<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;

class CheckoutController {
    private $auth;
    private $cartRepo;
    private $productRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
    }
    
    public function show() {
        $user = $this->auth->requireAuth();
        
        // Achat direct d'un produit
        if (isset($_GET['product'])) {
            $product = $this->productRepo->findById($_GET['product']);
            
            if (!$product) {
                $_SESSION['flash_error'] = 'Produit introuvable';
                header('Location: /produits');
                exit;
            }
            
            $items = [$product];
            $total = $product['price'];
        } else {
            // Achat depuis le panier
            $items = $this->cartRepo->getCartItems($user['id']);
            $total = $this->cartRepo->getCartTotal($user['id']);
            
            if (empty($items)) {
                $_SESSION['flash_error'] = 'Votre panier est vide';
                header('Location: /panier');
                exit;
            }
        }
        
        view('checkout/index', [
            'user' => $user,
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function create() {
        $user = $this->auth->requireAuth();
        
        // TODO: Créer la commande
        $_SESSION['flash_success'] = 'Commande créée ! (En développement)';
        header('Location: /compte/achats');
        exit;
    }
    
    public function processStripe() {
        $user = $this->auth->requireAuth();
        
        // TODO: Intégrer Stripe
        $_SESSION['flash_error'] = 'Paiement Stripe en cours de développement';
        header('Location: /checkout');
        exit;
    }
    
    public function processPaypal() {
        $user = $this->auth->requireAuth();
        
        // TODO: Intégrer PayPal
        $_SESSION['flash_error'] = 'Paiement PayPal en cours de développement';
        header('Location: /checkout');
        exit;
    }
    
    public function success($orderNumber) {
        $user = $this->auth->requireAuth();
        
        view('checkout/success', [
            'user' => $user,
            'orderNumber' => $orderNumber
        ]);
    }
    
    public function cancelled() {
        $user = $this->auth->requireAuth();
        
        view('checkout/cancelled', [
            'user' => $user
        ]);
    }
}