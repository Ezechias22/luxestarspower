<?php
namespace App\Controllers;

use App\Repositories\ProductRepository;

class HomeController {
    private $productRepo;
    
    public function __construct() {
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        $featured = $this->productRepo->getFeatured(12);
        $recent = $this->productRepo->getAllPaginated(1, 12, ['sort' => 'created_at DESC']);
        
        return $this->render('front/home', [
            'featured' => $featured,
            'recent' => $recent
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
