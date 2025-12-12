<?php
namespace App\Controllers\Seller;

use App\Services\AuthService;
use App\Repositories\OrderRepository;

class OrderController {
    private $auth;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
    }
    
    public function index() {
        $user = $this->auth->requireRole('seller');
        $page = $_GET['page'] ?? 1;
        
        $orders = $this->orderRepo->getBySeller($user->id, $page, 20);
        
        return $this->render('seller/orders', ['orders' => $orders, 'page' => $page]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
