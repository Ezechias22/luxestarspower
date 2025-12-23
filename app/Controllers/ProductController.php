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
        
        view('front/products/index', [
            'products' => $products['data'] ?? $products,
            'page' => $page,
            'filters' => $filters,
            'total' => $products['total'] ?? count($products)
        ]);
    }
    
    public function show($params) {
        // Extrait le slug du tableau de paramètres
        $slug = $params['slug'] ?? null;
        
        if (!$slug) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        
        $product = $this->productRepo->findBySlug($slug);
        
        if (!$product) {
            http_response_code(404);
            view('errors/404');
            return;
        }
        
        $this->productRepo->incrementViews($product['id']);
        $seller = $this->userRepo->findById($product['seller_id']);
        
        view('front/products/show', [
            'product' => $product,
            'seller' => $seller
        ]);
    }
}