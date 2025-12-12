<?php
namespace App\Controllers;

use App\Services\AuthService;

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    // Méthode pour routes.php: showLogin
    public function showLogin() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/login');
    }
    
    // Méthode pour routes.php: login
    public function login() {
        try {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $this->auth->login($email, $password);
            
            header('Location: /compte');
            exit;
        } catch (\Exception $e) {
            view('front/auth/login', ['error' => $e->getMessage()]);
        }
    }
    
    // Méthode pour routes.php: showRegister
    public function showRegister() {
        if ($this->auth->isLoggedIn()) {
            header('Location: /compte');
            exit;
        }
        view('front/auth/register');
    }
    
    // Méthode pour routes.php: register
    public function register() {
        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (strlen($password) < 8) {
                throw new \Exception("Le mot de passe doit contenir au moins 8 caractères");
            }
            
            $user = $this->auth->register($name, $email, $password);
            $this->auth->login($email, $password);
            
            header('Location: /compte');
            exit;
        } catch (\Exception $e) {
            view('front/auth/register', ['error' => $e->getMessage()]);
        }
    }
    
    // Méthode pour routes.php: logout
    public function logout() {
        $this->auth->logout();
        header('Location: /');
        exit;
    }
}