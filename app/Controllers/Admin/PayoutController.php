<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;

class PayoutController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        view('admin/payouts/index', [
            'user' => $user,
            'payouts' => []
        ]);
    }
    
    public function approve($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $_SESSION['flash_success'] = 'Paiement approuvé';
        header('Location: /admin/paiements');
        exit;
    }
    
    public function reject($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $_SESSION['flash_success'] = 'Paiement rejeté';
        header('Location: /admin/paiements');
        exit;
    }
}