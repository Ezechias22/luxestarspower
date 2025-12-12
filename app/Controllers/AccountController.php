<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\OrderRepository;

class AccountController {
    private $auth;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
    }
    
    public function dashboard() {
        $user = $this->auth->requireAuth();
        
        // Récupérer quelques statistiques
        $totalOrders = 0; // À implémenter
        $totalSpent = 0; // À implémenter
        
        view('account/dashboard', [
            'user' => $user,
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent
        ]);
    }
    
    public function purchases() {
        $user = $this->auth->requireAuth();
        $page = $_GET['page'] ?? 1;
        
        $orders = $this->orderRepo->getByBuyer($user['id'], $page);
        
        view('account/purchases', [
            'user' => $user,
            'orders' => $orders
        ]);
    }
    
    public function downloads() {
        $user = $this->auth->requireAuth();
        
        view('account/downloads', [
            'user' => $user,
            'downloads' => []
        ]);
    }
    
    public function settings() {
        $user = $this->auth->requireAuth();
        
        view('account/settings', [
            'user' => $user
        ]);
    }
    
    public function updateSettings() {
        $user = $this->auth->requireAuth();
        $userRepo = new \App\Repositories\UserRepository();
        
        $userRepo->update($user['id'], [
            'name' => $_POST['name'] ?? $user['name']
        ]);
        
        $_SESSION['user_name'] = $_POST['name'] ?? $user['name'];
        
        header('Location: /compte/parametres?success=1');
        exit;
    }
}