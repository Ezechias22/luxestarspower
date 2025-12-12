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
        
        if ($user && isset($user['role']) && ($user['role'] === 'seller' || $user['role'] === 'admin')) {
            header('Location: /vendeur/tableau-de-bord');
            exit;
        }
        
        view('seller/onboarding');
    }
    
    public function become() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /connexion');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $user = $this->userRepo->findById($userId);
        
        if ($user && isset($user['role']) && ($user['role'] === 'seller' || $user['role'] === 'admin')) {
            header('Location: /vendeur/tableau-de-bord');
            exit;
        }
        
        $this->userRepo->updateRole($userId, 'seller');
        $_SESSION['user_role'] = 'seller';
        
        header('Location: /vendeur/tableau-de-bord');
        exit;
    }
}