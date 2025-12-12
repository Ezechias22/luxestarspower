<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\UserRepository;

class SellerController {
    private $auth;
    private $userRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->userRepo = new UserRepository();
    }
    
    public function onboarding() {
        $user = $this->auth->getCurrentUser();
        
        if ($user && $user->isSeller()) {
            header('Location: /vendeur/dashboard');
            exit;
        }
        
        return $this->render('front/seller/onboarding');
    }
    
    public function becomeSeller() {
        $user = $this->auth->requireAuth();
        
        if ($user->isSeller()) {
            header('Location: /vendeur/dashboard');
            exit;
        }
        
        $this->userRepo->updateRole($user->id, 'seller');
        $_SESSION['user_role'] = 'seller';
        
        header('Location: /vendeur/dashboard');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
