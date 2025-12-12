<?php
namespace App\Controllers;

use App\Repositories\ProductRepository;

class SearchController {
    private $productRepo;
    
    public function __construct() {
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        $query = $_GET['q'] ?? '';
        $page = $_GET['page'] ?? 1;
        
        $products = [];
        if (strlen($query) >= 2) {
            $products = $this->productRepo->getAllPaginated($page, 20, ['search' => $query]);
        }
        
        return $this->render('front/search', [
            'products' => $products,
            'query' => $query,
            'page' => $page
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
