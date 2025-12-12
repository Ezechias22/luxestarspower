<?php
namespace App\Controllers;

use App\Services\{AuthService, DownloadService};

class DownloadController {
    private $auth;
    private $download;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->download = new DownloadService();
    }
    
    public function serve($token) {
        try {
            $url = $this->download->getSecureDownloadUrl($token);
            header("Location: $url");
            exit;
        } catch (\Exception $e) {
            http_response_code(403);
            return $this->render('errors/403', ['message' => $e->getMessage()]);
        }
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
