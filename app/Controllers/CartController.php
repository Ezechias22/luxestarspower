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

    /**
     * Affiche le panier
     */
    public function index() {
        // Utilise requireAuth() qui retourne l'utilisateur
        $user = $this->auth->requireAuth();

        $cartItems = $this->cartRepo->getCartItems($user['id']);
        $total = $this->cartRepo->getCartTotal($user['id']);

        view('cart/index', [
            'user' => $user,
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    /**
     * Ajoute un produit au panier
     */
    public function add() {
        // Vérifie si l'utilisateur est connecté
        if (!$this->auth->isLoggedIn()) {
            // Sauvegarde l'action pour après connexion
            $_SESSION['pending_cart_action'] = [
                'action' => 'add',
                'product_id' => $_POST['product_id'] ?? null,
                'quantity' => $_POST['quantity'] ?? 1,
                'return_url' => $_SERVER['HTTP_REFERER'] ?? '/produits'
            ];
            $_SESSION['redirect_after_login'] = '/panier';
            header('Location: /connexion');
            exit;
        }

        // Récupère l'utilisateur
        $user = $this->auth->getCurrentUser();

        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 1);

        if (!$productId) {
            $_SESSION['flash_error'] = 'Produit invalide';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/produits'));
            exit;
        }

        if ($quantity < 1) {
            $_SESSION['flash_error'] = 'Quantité invalide';
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/produits'));
            exit;
        }

        try {
            $this->cartRepo->addToCart($user['id'], $productId, $quantity);
            $_SESSION['flash_success'] = 'Produit ajouté au panier ! 🛒';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        // Redirige vers la page précédente ou le panier
        $redirect = $_POST['redirect'] ?? $_SERVER['HTTP_REFERER'] ?? '/panier';
        header('Location: ' . $redirect);
        exit;
    }

    /**
     * Supprime un produit du panier
     */
    public function remove($params) {
        $user = $this->auth->requireAuth();

        $cartItemId = $params['id'] ?? null;
        
        if (!$cartItemId) {
            $_SESSION['flash_error'] = 'ID invalide';
            header('Location: /panier');
            exit;
        }

        try {
            $this->cartRepo->removeFromCart($user['id'], $cartItemId);
            $_SESSION['flash_success'] = 'Produit retiré du panier';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /panier');
        exit;
    }

    /**
     * Met à jour la quantité d'un produit
     */
    public function updateQuantity() {
        $user = $this->auth->requireAuth();

        $productId = $_POST['product_id'] ?? null;
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (!$productId) {
            $_SESSION['flash_error'] = 'Produit invalide';
            header('Location: /panier');
            exit;
        }

        if ($quantity < 0) {
            $_SESSION['flash_error'] = 'Quantité invalide';
            header('Location: /panier');
            exit;
        }

        try {
            if ($quantity == 0) {
                // Si quantité = 0, supprime du panier
                $this->cartRepo->removeFromCart($user['id'], $productId);
                $_SESSION['flash_success'] = 'Produit retiré du panier';
            } else {
                $this->cartRepo->updateQuantity($user['id'], $productId, $quantity);
                $_SESSION['flash_success'] = 'Quantité mise à jour';
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /panier');
        exit;
    }

    /**
     * Vide complètement le panier
     */
    public function clear() {
        $user = $this->auth->requireAuth();

        try {
            $this->cartRepo->clearCart($user['id']);
            $_SESSION['flash_success'] = 'Panier vidé';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /panier');
        exit;
    }

    /**
     * Compte le nombre d'articles dans le panier (pour le badge)
     */
    public function getCartCount() {
        if (!$this->auth->isLoggedIn()) {
            return 0;
        }

        $user = $this->auth->getCurrentUser();
        
        if (!$user) {
            return 0;
        }

        try {
            return $this->cartRepo->getCartItemsCount($user['id']);
        } catch (\Exception $e) {
            return 0;
        }
    }
}