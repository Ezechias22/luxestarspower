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
    
    /**
     * Affiche la page de checkout
     */
    public function show() {
        // Requiert l'authentification et récupère l'utilisateur
        $user = $this->auth->requireAuth();

        // Achat direct d'un produit
        if (isset($_GET['product'])) {
            $product = $this->productRepo->findById($_GET['product']);

            if (!$product) {
                $_SESSION['flash_error'] = 'Produit introuvable';
                header('Location: /produits');
                exit;
            }

            if (!$product['is_active']) {
                $_SESSION['flash_error'] = 'Ce produit n\'est plus disponible';
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
    
    /**
     * Traite le paiement via Stripe
     */
    public function processStripe() {
        $user = $this->auth->requireAuth();
        
        try {
            // Récupère les items à acheter
            if (isset($_GET['product'])) {
                // Achat direct d'un produit
                $product = $this->productRepo->findById($_GET['product']);
                if (!$product) {
                    throw new \Exception('Produit introuvable');
                }
                if (!$product['is_active']) {
                    throw new \Exception('Ce produit n\'est plus disponible');
                }
                $product['quantity'] = 1;
                $items = [$product];
                $total = $product['price'];
            } else {
                // Achat depuis le panier
                $items = $this->cartRepo->getCartItems($user['id']);
                $total = $this->cartRepo->getCartTotal($user['id']);
                
                if (empty($items)) {
                    throw new \Exception('Votre panier est vide');
                }
                
                // Normalise les items du panier
                foreach ($items as &$item) {
                    if (!isset($item['quantity'])) {
                        $item['quantity'] = 1;
                    }
                    // Si l'item vient du panier, il a product_id au lieu de id
                    if (!isset($item['id']) && isset($item['product_id'])) {
                        $item['id'] = $item['product_id'];
                    }
                    // Charge les infos complètes du produit pour avoir seller_id
                    if (!isset($item['seller_id'])) {
                        $fullProduct = $this->productRepo->findById($item['id']);
                        if ($fullProduct) {
                            $item['seller_id'] = $fullProduct['seller_id'];
                            $item['title'] = $fullProduct['title'];
                            $item['price'] = $fullProduct['price'];
                        }
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
                    $item['seller_id'] ?? $user['id'],
                    $item['price'],
                    $item['quantity'] ?? 1
                );
            }
            
            // Construit les URLs de retour
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            $host = str_replace('www.', '', $host);
            $baseUrl = $protocol . $host;
            
            $successUrl = $baseUrl . '/checkout/success?session_id={CHECKOUT_SESSION_ID}&order_id=' . $order['id'];
            $cancelUrl = $baseUrl . '/checkout/cancelled?order_id=' . $order['id'];
            
            // Crée la session Stripe Checkout
            $session = $this->stripe->createCheckoutSession($items, $user['id'], $successUrl, $cancelUrl);
            
            // Sauvegarde l'ID de session dans la commande
            $this->orderRepo->update($order['id'], [
                'stripe_session_id' => $session->id
            ]);
            
            // Redirige vers Stripe Checkout
            header('Location: ' . $session->url);
            exit;
            
        } catch (\Exception $e) {
            error_log("Stripe checkout error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /checkout');
            exit;
        }
    }
    
    /**
     * Page de succès après paiement
     */
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
            // Vérifie le paiement auprès de Stripe
            if ($this->stripe->isPaymentSuccessful($sessionId)) {
                // Met à jour la commande
                $this->orderRepo->update($orderId, [
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s')
                ]);
                
                // Vide le panier
                $this->cartRepo->clearCart($user['id']);
                
                // Récupère la commande
                $order = $this->orderRepo->findById($orderId);
                
                view('checkout/success', [
                    'user' => $user,
                    'order' => $order,
                    'orderNumber' => $order['id']
                ]);
            } else {
                throw new \Exception('Paiement non confirmé par Stripe');
            }
        } catch (\Exception $e) {
            error_log("Payment verification error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
            header('Location: /compte');
            exit;
        }
    }
    
    /**
     * Page d'annulation de paiement
     */
    public function cancelled() {
        $user = $this->auth->requireAuth();
        
        $orderId = $_GET['order_id'] ?? null;
        
        if ($orderId) {
            // Annule la commande
            $this->orderRepo->update($orderId, [
                'status' => 'cancelled',
                'payment_status' => 'failed',
                'cancelled_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        view('checkout/cancelled', [
            'user' => $user
        ]);
    }
    
    /**
     * Traite le paiement via PayPal (à implémenter)
     */
    public function processPaypal() {
        $user = $this->auth->requireAuth();
        
        // TODO: Intégrer PayPal
        $_SESSION['flash_error'] = 'Paiement PayPal en cours de développement';
        header('Location: /checkout');
        exit;
    }
}