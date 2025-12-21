<?php
namespace App\Controllers;

use App\Services\AuthService;

class PageController {
    private $auth;
    
    public function __construct() {
        $this->auth = new AuthService();
    }
    
    public function about() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/about', [
            'user' => $user
        ]);
    }
    
    public function contact() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/contact', [
            'user' => $user
        ]);
    }
    
    public function contactSubmit() {
        try {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';
            
            if (empty($name) || empty($email) || empty($message)) {
                throw new \Exception('Tous les champs sont requis');
            }
            
            // TODO: Envoyer l'email
            $_SESSION['flash_success'] = 'Message envoyé avec succès ! Nous vous répondrons rapidement.';
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }
        
        header('Location: /contact');
        exit;
    }
    
    public function faq() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/faq', [
            'user' => $user
        ]);
    }
    
    public function terms() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/terms', [
            'user' => $user
        ]);
    }
    
    public function privacy() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/privacy', [
            'user' => $user
        ]);
    }
    
    public function refund() {
        $user = $this->auth->getCurrentUser();
        
        view('front/pages/refund', [
            'user' => $user
        ]);
    }
}