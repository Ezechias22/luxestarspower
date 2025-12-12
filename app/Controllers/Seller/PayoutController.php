<?php
namespace App\Controllers\Seller;

use App\Services\AuthService;
use App\Repositories\OrderRepository;
use App\Database;

class PayoutController {
    private $auth;
    private $orderRepo;
    private $db;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
        $this->db = Database::getInstance();
    }
    
    public function index() {
        $user = $this->auth->requireRole('seller');
        
        $pendingEarnings = $this->orderRepo->getSellerEarnings($user->id);
        
        $payouts = $this->db->fetchAll(
            "SELECT * FROM payouts WHERE seller_id = ? ORDER BY created_at DESC LIMIT 50",
            [$user->id]
        );
        
        return $this->render('seller/payouts', [
            'payouts' => $payouts,
            'pending' => $pendingEarnings
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
