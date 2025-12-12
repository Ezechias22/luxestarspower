<?php
namespace App\Controllers\Seller;

use App\Services\AuthService;
use App\Repositories\{ProductRepository, OrderRepository};

class DashboardController {
    private $auth;
    private $productRepo;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
        $this->orderRepo = new OrderRepository();
    }
    
    public function index() {
        $user = $this->auth->requireRole('seller');
        
        $stats = [
            'total_products' => count($this->productRepo->getAllPaginated(1, 1000, ['seller_id' => $user->id])),
            'total_sales' => $this->orderRepo->getSellerEarnings($user->id),
            'recent_orders' => $this->orderRepo->getBySeller($user->id, 1, 10)
        ];
        
        return $this->render('seller/dashboard', ['stats' => $stats, 'user' => $user]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
