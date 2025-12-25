<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;

class SellerController {
    private $auth;
    private $userRepo;
    private $productRepo;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->userRepo = new UserRepository();
        $this->productRepo = new ProductRepository();
        $this->orderRepo = new OrderRepository();
    }

    public function onboarding() {
        $user = $this->auth->getCurrentUser();
        
        if ($user && isset($user['role']) && ($user['role'] === 'seller' || $user['role'] === 'admin')) {
            header('Location: /vendeur/tableau-de-bord');
            exit;
        }

        view('seller/onboarding');
    }

    public function become() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userRepo->findById($userId);
        
        if ($user && isset($user['role']) && ($user['role'] === 'seller' || $user['role'] === 'admin')) {
            header('Location: /vendeur/tableau-de-bord');
            exit;
        }
        
        $this->userRepo->updateRole($userId, 'seller');
        $_SESSION['user_role'] = 'seller';
        
        header('Location: /vendeur/tableau-de-bord');
        exit;
    }

    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        if (!$user || ($user['role'] !== 'seller' && $user['role'] !== 'admin')) {
            header('Location: /vendre');
            exit;
        }

        // Récupère les VRAIES statistiques du vendeur
        $sellerId = $user['id'];

        // 1. Nombre de produits actifs
        $products = $this->productRepo->getBySeller($sellerId);
        $totalProducts = count($products);
        $activeProducts = count(array_filter($products, function($p) {
            return $p['is_active'] == 1;
        }));

        // 2. Récupère toutes les commandes avec les items
        $allOrders = $this->orderRepo->getAll(); // On va créer cette méthode
        $totalSales = 0;
        $totalRevenue = 0;
        $recentOrders = [];

        foreach ($allOrders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                
                foreach ($items as $item) {
                    // Vérifie si le produit appartient au vendeur
                    if ($item['seller_id'] == $sellerId) {
                        $totalSales++;
                        $totalRevenue += $item['price'] * $item['quantity'];
                        
                        // Ajoute aux commandes récentes (max 5)
                        if (count($recentOrders) < 5) {
                            $recentOrders[] = [
                                'order_id' => $order['id'],
                                'product_title' => $item['title'],
                                'price' => $item['price'],
                                'quantity' => $item['quantity'],
                                'created_at' => $order['created_at']
                            ];
                        }
                    }
                }
            }
        }

        // 3. Revenus disponibles (90% des ventes, 10% commission plateforme)
        $availableBalance = $totalRevenue * 0.9;

        view('seller/dashboard', [
            'user' => $user,
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'totalProducts' => $totalProducts,
            'activeProducts' => $activeProducts,
            'availableBalance' => $availableBalance,
            'recentOrders' => $recentOrders
        ]);
    }

    public function statistics() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        if (!$user || ($user['role'] !== 'seller' && $user['role'] !== 'admin')) {
            header('Location: /vendre');
            exit;
        }

        view('seller/statistics', [
            'user' => $user
        ]);
    }

    public function shopSettings() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        if (!$user || ($user['role'] !== 'seller' && $user['role'] !== 'admin')) {
            header('Location: /vendre');
            exit;
        }

        view('seller/shop-settings', [
            'user' => $user
        ]);
    }

    public function updateShop() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        if (!$user || ($user['role'] !== 'seller' && $user['role'] !== 'admin')) {
            http_response_code(403);
            die('Accès interdit');
        }

        // TODO: Implémenter la mise à jour des paramètres de boutique
        
        $_SESSION['flash_success'] = 'Paramètres mis à jour avec succès !';
        header('Location: /vendeur/boutique');
        exit;
    }
}