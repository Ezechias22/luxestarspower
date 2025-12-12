<?php
namespace App\Controllers;

use App\Repositories\ProductRepository;
use App\Repositories\UserRepository;

class ProductController {
    private $productRepo;
    private $userRepo;
    
    public function __construct() {
        $this->productRepo = new ProductRepository();
        $this->userRepo = new UserRepository();
    }
    
    public function index() {
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $type = $_GET['type'] ?? '';
        $search = $_GET['q'] ?? '';
        
        $filters = [];
        if ($type) $filters['type'] = $type;
        if ($search) $filters['search'] = $search;
        
        $products = $this->productRepo->getAllPaginated($page, 20, $filters);
        
        return $this->render('front/products/index', [
            'products' => $products,
            'page' => $page,
            'filters' => $filters
        ]);
    }
    
    public function show($slug) {
        $product = $this->productRepo->findBySlug($slug);
        
        if (!$product) {
            http_response_code(404);
            return $this->render('errors/404');
        }
        
        $this->productRepo->incrementViews($product->id);
        $seller = $this->userRepo->findById($product->seller_id);
        
        return $this->render('front/products/show', [
            'product' => $product,
            'seller' => $seller
        ]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
