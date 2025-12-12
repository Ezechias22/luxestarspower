<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\{UserRepository, ProductRepository, OrderRepository};

class DashboardController {
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
    
    public function index() {
        $this->auth->requireRole('admin');
        
        $stats = [
            'total_users' => $this->userRepo->count([]),
            'total_sellers' => $this->userRepo->count(['role' => 'seller']),
            'total_revenue' => $this->orderRepo->getTotalRevenue(),
            'recent_orders' => $this->orderRepo->getAll(1, 10)
        ];
        
        return $this->render('admin/dashboard', ['stats' => $stats]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
