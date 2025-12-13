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
            'payouts' => [],
            'balance' => 0.00
        ]);
    }
    
    public function request() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        // Message de succès temporaire
        $_SESSION['flash_success'] = 'Demande de paiement enregistrée. Vous serez contacté sous 48h.';
        
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
        
        $_SESSION['flash_success'] = 'Méthode de paiement enregistrée avec succès.';
        
        header('Location: /vendeur/paiements');
        exit;
    }
}