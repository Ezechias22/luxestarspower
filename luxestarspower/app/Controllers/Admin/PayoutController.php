<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Database;

class PayoutController {
    private $auth;
    private $db;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->db = Database::getInstance();
    }
    
    public function index() {
        $this->auth->requireRole('admin');
        $page = $_GET['page'] ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $payouts = $this->db->fetchAll(
            "SELECT p.*, u.name as seller_name, u.email as seller_email FROM payouts p JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
        return $this->render('admin/payouts', ['payouts' => $payouts, 'page' => $page]);
    }
    
    public function process($id) {
        $this->auth->requireRole('admin');
        
        $this->db->query(
            "UPDATE payouts SET status = 'paid', processed_at = NOW() WHERE id = ?",
            [$id]
        );
        
        header('Location: /admin/payouts');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
