<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\UserRepository;

class DashboardController {
    private $auth;
    private $userRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->userRepo = new UserRepository();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit - Administrateur uniquement');
        }
        
        $stats = [
            'total_users' => $this->userRepo->count([]),
            'total_sellers' => $this->userRepo->count(['role' => 'seller']),
            'total_products' => 0,
            'total_orders' => 0
        ];
        
        view('admin/dashboard', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
}