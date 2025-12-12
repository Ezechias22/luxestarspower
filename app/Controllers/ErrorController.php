<?php
namespace App\Controllers;

class ErrorController {
    public function notFound() {
        http_response_code(404);
        return $this->render('errors/404');
    }
    
    public function forbidden() {
        http_response_code(403);
        return $this->render('errors/403');
    }
    
    public function serverError() {
        http_response_code(500);
        return $this->render('errors/500');
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
