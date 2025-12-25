<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;

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
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit - Administrateur uniquement');
        }

        // Récupère les vraies statistiques
        $allProducts = $this->productRepo->getAllPaginated(1, 999999, []);
        $allOrders = $this->orderRepo->getAll();

        $stats = [
            'total_users' => $this->userRepo->count([]),
            'total_sellers' => $this->userRepo->count(['role' => 'seller']),
            'total_products' => $allProducts['total'] ?? 0,
            'total_orders' => count($allOrders)
        ];

        view('admin/dashboard', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
}