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
        $allOrders = $this->orderRepo->getAll();
        $totalSales = 0;
        $totalRevenue = 0;
        $recentOrders = [];

        foreach ($allOrders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                
                foreach ($items as $item) {
                    if ($item['seller_id'] == $sellerId) {
                        $totalSales++;
                        $totalRevenue += $item['price'] * $item['quantity'];
                        
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

        // 3. Revenus disponibles (90% des ventes, 10% commission)
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

        $_SESSION['flash_success'] = 'Paramètres mis à jour avec succès !';
        header('Location: /vendeur/boutique');
        exit;
    }

    /**
     * Affiche la page de paramètres du vendeur
     */
    public function settings() {
        $this->auth->requireSeller();
        
        $user = $this->userRepo->findById($_SESSION['user_id']);
        
        if (!$user) {
            header('Location: /connexion');
            exit;
        }
        
        view('seller/settings', [
            'user' => $user,
            'title' => 'Paramètres du compte'
        ]);
    }

    /**
     * Met à jour le profil du vendeur
     */
    public function updateProfile() {
        $this->auth->requireSeller();
        
        try {
            $userId = $_SESSION['user_id'];
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'bio' => trim($_POST['bio'] ?? ''),
            ];
            
            // Validation
            if (empty($data['name'])) {
                throw new \Exception("Le nom est requis");
            }
            
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Email invalide");
            }
            
            // Vérifie si l'email est déjà utilisé
            $existingUser = $this->userRepo->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                throw new \Exception("Cet email est déjà utilisé");
            }
            
            // Met à jour
            $this->userRepo->update($userId, $data);
            
            // Met à jour la session
            $_SESSION['user_name'] = $data['name'];
            $_SESSION['user_email'] = $data['email'];
            
            $_SESSION['success'] = "Profil mis à jour avec succès !";
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /vendeur/parametres');
        exit;
    }

    /**
     * Met à jour les informations de la boutique
     */
    public function updateShopInfo() {
        $this->auth->requireSeller();
        
        try {
            $userId = $_SESSION['user_id'];
            
            $data = [
                'shop_name' => trim($_POST['shop_name'] ?? ''),
                'shop_slug' => trim($_POST['shop_slug'] ?? ''),
                'shop_description' => trim($_POST['shop_description'] ?? ''),
            ];
            
            // Validation
            if (empty($data['shop_name'])) {
                throw new \Exception("Le nom de la boutique est requis");
            }
            
            if (empty($data['shop_slug'])) {
                throw new \Exception("L'URL de la boutique est requise");
            }
            
            // Valide le format du slug
            if (!preg_match('/^[a-z0-9-]+$/', $data['shop_slug'])) {
                throw new \Exception("L'URL ne peut contenir que des lettres minuscules, chiffres et tirets");
            }
            
            // Vérifie si le slug est déjà utilisé
            if ($this->userRepo->shopSlugExists($data['shop_slug'], $userId)) {
                throw new \Exception("Cette URL de boutique est déjà utilisée");
            }
            
            // Met à jour (shop_ et store_ automatiquement synchronisés)
            $this->userRepo->update($userId, $data);
            
            // Met à jour la session
            $_SESSION['user_shop_slug'] = $data['shop_slug'];
            
            $_SESSION['success'] = "Boutique mise à jour avec succès !";
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /vendeur/parametres');
        exit;
    }

    /**
     * Change le mot de passe
     */
    public function updatePassword() {
        $this->auth->requireSeller();
        
        try {
            $userId = $_SESSION['user_id'];
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validation
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                throw new \Exception("Tous les champs sont requis");
            }
            
            if ($newPassword !== $confirmPassword) {
                throw new \Exception("Les nouveaux mots de passe ne correspondent pas");
            }
            
            if (strlen($newPassword) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
            }
            
            // Vérifie l'ancien mot de passe
            $user = $this->userRepo->findById($userId);
            if (!password_verify($currentPassword, $user['password_hash'])) {
                throw new \Exception("Mot de passe actuel incorrect");
            }
            
            // Met à jour
            $this->userRepo->update($userId, [
                'password_hash' => password_hash($newPassword, PASSWORD_ARGON2ID)
            ]);
            
            $_SESSION['success'] = "Mot de passe changé avec succès !";
            
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        header('Location: /vendeur/parametres');
        exit;
    }
}