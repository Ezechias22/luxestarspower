<?php
namespace App\Controllers;

use App\Services\AuthService;

class PayoutController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        view('seller/payouts/index', [
            'user' => $user,
            'payouts' => []
        ]);
    }
    
    public function request() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        header('Location: /vendeur/paiements');
        exit;
    }
    
    public function setupMethod() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        view('seller/payouts/setup', [
            'user' => $user
        ]);
    }
    
    public function saveMethod() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        header('Location: /vendeur/paiements');
        exit;
    }
}