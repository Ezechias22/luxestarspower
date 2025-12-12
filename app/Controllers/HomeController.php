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
        
        view('front/home', [
            'featuredProducts' => $featured,
            'latestProducts' => $recent['data'] ?? $recent
        ]);
    }
}