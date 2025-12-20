<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\StripeService;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;

class CheckoutController {
    private $auth;
    private $cartRepo;
    private $productRepo;
    private $orderRepo;
    private $stripe;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->cartRepo = new CartRepository();
        $this->productRepo = new ProductRepository();
        $this->orderRepo = new OrderRepository();
        $this->stripe = new StripeService();
    }
    
    public function show() {
        // Sauvegarde l'URL pour redirection après login
        if (!isset($_SESSION['user'])) {
            $_SESSION['redirect_after_login'] = '/checkout' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '');
            header('Location: /connexion');
            exit;
        }

        $user = $this->auth->requireAuth();

        // Achat direct d'un produit
        if (isset($_GET['product'])) {
            $product = $this->productRepo->findById($_GET['product']);

            if (!$product) {
                $_SESSION['flash_error'] = 'Produit introuvable';
                header('Location: /produits');
                exit;
            }
            
            $product['quantity'] = 1;
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
            
            // Ajoute quantity si elle n'existe pas
            foreach ($items as &$item) {
                if (!isset($item['quantity'])) {
                    $item['quantity'] = 1;
                }
            }
        }

        view('checkout/index', [
            'user' => $user,
            'items' => $items,
            'total' => $total
        ]);
    }
    
    public function processStripe() {
        $user = $this->auth->requireAuth();
        
        try {
            // Récupère les items
            if (isset($_GET['product'])) {
                $product = $this->productRepo->findById($_GET['product']);
                if (!$product) {
                    throw new \Exception('Produit introuvable');
                }
                $product['quantity'] = 1;
                $items = [$product];
                $total = $product['price'];
            } else {
                $items = $this->cartRepo->getCartItems($user['id']);
                $total = $this->cartRepo->getCartTotal($user['id']);
                
                if (empty($items)) {
                    throw new \Exception('Votre panier est vide');
                }
                
                foreach ($items as &$item) {
                    if (!isset($item['quantity'])) {
                        $item['quantity'] = 1;
                    }
                }
            }
            
            // Crée la commande en attente
            $order = $this->orderRepo->create([
                'user_id' => $user['id'],
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => 'stripe',
                'payment_status' => 'pending'
            ]);
            
            // Ajoute les items à la commande
            foreach ($items as $item) {
                $this->orderRepo->addOrderItem(
                    $order['id'],
                    $item['id'],
                    $item['seller_id'],
                    $item['price'],
                    $item['quantity'] ?? 1
                );
            }
            
            // URLs de retour
            $config = require __DIR__ . '/../../config/config.php';
            $baseUrl = $config['app']['url'];
            $successUrl = $baseUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order['id'];
            $cancelUrl = $baseUrl . '/checkout/cancelled?order_id=' . $order['id'];
            
            // Crée la session Stripe
            $session = $this->stripe->createCheckoutSession($items, $user['id'], $successUrl, $cancelUrl);
            
            // Redirige vers Stripe Checkout
            header('Location: ' . $session->url);
            exit;
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /checkout');
            exit;
        }
    }
    
    public function success() {
        $user = $this->auth->requireAuth();
        
        $sessionId = $_GET['session_id'] ?? null;
        $orderId = $_GET['order_id'] ?? null;
        
        if (!$sessionId || !$orderId) {
            $_SESSION['flash_error'] = 'Session invalide';
            header('Location: /compte');
            exit;
        }
        
        try {
            // Vérifie le paiement
            if ($this->stripe->isPaymentSuccessful($sessionId)) {
                // Met à jour la commande
                $this->orderRepo->update($orderId, [
                    'status' => 'completed',
                    'payment_status' => 'paid'
                ]);
                
                // Vide le panier
                $this->cartRepo->clearCart($user['id']);
                
                $order = $this->orderRepo->findById($orderId);
                
                view('checkout/success', [
                    'user' => $user,
                    'order' => $order,
                    'orderNumber' => $order['id']
                ]);
            } else {
                throw new \Exception('Paiement non confirmé');
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /compte');
            exit;
        }
    }
    
    public function cancelled() {
        $user = $this->auth->requireAuth();
        
        $orderId = $_GET['order_id'] ?? null;
        
        if ($orderId) {
            // Annule la commande
            $this->orderRepo->update($orderId, [
                'status' => 'cancelled',
                'payment_status' => 'failed'
            ]);
        }
        
        view('checkout/cancelled', [
            'user' => $user
        ]);
    }
    
    public function processPaypal() {
        $user = $this->auth->requireAuth();
        
        // TODO: Intégrer PayPal
        $_SESSION['flash_error'] = 'Paiement PayPal en cours de développement';
        header('Location: /checkout');
        exit;
    }
}