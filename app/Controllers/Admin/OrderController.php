<?php
namespace App\Controllers\Admin;

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
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        view('admin/orders/index', [
            'user' => $user,
            'orders' => []
        ]);
    }
    
    public function show($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        view('admin/orders/show', [
            'user' => $user,
            'order' => []
        ]);
    }
    
    public function refund($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $_SESSION['flash_success'] = 'Remboursement effectué';
        header('Location: /admin/commandes');
        exit;
    }
}