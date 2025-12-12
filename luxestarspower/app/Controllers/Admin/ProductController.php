<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\ProductRepository;

class ProductController {
    private $auth;
    private $productRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        $this->auth->requireRole('admin');
        $page = $_GET['page'] ?? 1;
        
        $products = $this->productRepo->getAllPaginated($page, 20);
        
        return $this->render('admin/products/index', ['products' => $products]);
    }
    
    public function toggle($id) {
        $this->auth->requireRole('admin');
        $this->productRepo->toggleActive($id);
        
        header('Location: /admin/products');
        exit;
    }
    
    public function feature($id) {
        $this->auth->requireRole('admin');
        $this->productRepo->toggleFeatured($id);
        
        header('Location: /admin/products');
        exit;
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
