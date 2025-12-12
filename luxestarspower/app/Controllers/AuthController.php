<?php
namespace App\Controllers;

use App\Services\AuthService;

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    public function loginForm() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        return $this->render('front/auth/login');
    }
    
    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $this->auth->login($email, $password);
            header('Location: /compte');
            exit;
        } catch (\Exception $e) {
            return $this->render('front/auth/login', ['error' => $e->getMessage()]);
        }
    }
    
    public function registerForm() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        return $this->render('front/auth/register');
    }
    
    public function register() {
        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (strlen($password) < 8) {
                throw new \Exception("Password must be at least 8 characters");
            }
            
            $user = $this->auth->register($name, $email, $password);
            $this->auth->login($email, $password);
            
            header('Location: /compte');
            exit;
        } catch (\Exception $e) {
            return $this->render('front/auth/register', ['error' => $e->getMessage()]);
        }
    }
    
    public function logout() {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
