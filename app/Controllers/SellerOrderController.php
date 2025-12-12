<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\OrderRepository;

class SellerOrderController {
    private $auth;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        view('seller/orders/index', [
            'user' => $user,
            'orders' => []
        ]);
    }
    
    public function show($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        view('seller/orders/show', [
            'user' => $user,
            'order' => []
        ]);
    }
}