<?php
namespace App\Controllers;

use App\Services\{AuthService, DownloadService};
use App\Repositories\OrderRepository;

class AccountController {
    private $auth;
    private $orderRepo;
    private $download;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
        $this->download = new DownloadService();
    }
    
    public function dashboard() {
        $user = $this->auth->requireAuth();
        return $this->render('front/account/dashboard', ['user' => $user]);
    }
    
    public function purchases() {
        $user = $this->auth->requireAuth();
        $page = $_GET['page'] ?? 1;
        $orders = $this->orderRepo->getByBuyer($user->id, $page);
        return $this->render('front/account/purchases', ['orders' => $orders]);
    }
    
    public function downloads() {
        $user = $this->auth->requireAuth();
        $page = $_GET['page'] ?? 1;
        $downloads = $this->download->getUserDownloads($user->id, $page);
        return $this->render('front/account/downloads', ['downloads' => $downloads]);
    }
    
    public function settings() {
        $user = $this->auth->requireAuth();
        return $this->render('front/account/settings', ['user' => $user]);
    }
    
    public function updateSettings() {
        $user = $this->auth->requireAuth();
        $userRepo = new \App\Repositories\UserRepository();
        
        $userRepo->update($user->id, [
            'name' => $_POST['name'] ?? $user->name,
            'bio' => $_POST['bio'] ?? $user->bio,
            'currency' => $_POST['currency'] ?? $user->currency
        ]);
        
        header('Location: /compte/parametres');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
