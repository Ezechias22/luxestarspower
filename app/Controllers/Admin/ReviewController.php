<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;

class ReviewController {
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
        
        view('admin/reviews/index', [
            'user' => $user,
            'reviews' => []
        ]);
    }
}