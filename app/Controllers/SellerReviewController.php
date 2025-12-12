<?php
namespace App\Controllers;

use App\Services\AuthService;

class SellerReviewController {
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
        
        view('seller/reviews/index', [
            'user' => $user,
            'reviews' => []
        ]);
    }
}