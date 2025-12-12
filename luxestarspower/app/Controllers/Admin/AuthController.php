<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    public function loginForm() {
        if ($this->auth->isLoggedIn()) {
            $user = $this->auth->getCurrentUser();
            if ($user->isAdmin()) {
                header('Location: /admin/dashboard');
                exit;
            }
        }
        return $this->render('admin/login');
    }
    
    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $user = $this->auth->login($email, $password);
            
            if (!$user->isAdmin()) {
                $this->auth->logout();
                throw new \Exception("Access denied - Admin only");
            }
            
            header('Location: /admin/dashboard');
            exit;
        } catch (\Exception $e) {
            return $this->render('admin/login', ['error' => $e->getMessage()]);
        }
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
