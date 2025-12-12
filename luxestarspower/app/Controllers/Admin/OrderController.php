<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\OrderRepository;
use App\Database;

class OrderController {
    private $auth;
    private $orderRepo;
    private $db;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
        $this->db = Database::getInstance();
    }
    
    public function index() {
        $this->auth->requireRole('admin');
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        
        $filters = [];
        if ($status) $filters['status'] = $status;
        
        $orders = $this->orderRepo->getAll($page, 20, $filters);
        
        return $this->render('admin/orders', ['orders' => $orders, 'page' => $page]);
    }
    
    public function refund($id) {
        $this->auth->requireRole('admin');
        
        $order = $this->orderRepo->findById($id);
        if ($order && $order->status === 'paid') {
            $this->orderRepo->updateStatus($id, 'refunded');
            
            $this->db->insert(
                "INSERT INTO transactions (user_id, order_id, type, amount, balance_after, created_at) VALUES (?, ?, 'refund', ?, 0, NOW())",
                [$order->seller_id, $order->id, -$order->seller_earnings]
            );
        }
        
        header('Location: /admin/orders');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
