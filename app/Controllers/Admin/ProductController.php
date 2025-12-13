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
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $page = $_GET['page'] ?? 1;
        $result = $this->productRepo->getAllPaginated($page, 20, []);
        
        view('admin/products/index', [
            'user' => $user,
            'products' => $result['data'] ?? [],
            'currentPage' => $page
        ]);
    }
    
    public function show($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $product = $this->productRepo->findById($id);
        
        if (!$product) {
            http_response_code(404);
            die('Produit non trouvé');
        }
        
        view('admin/products/show', [
            'user' => $user,
            'product' => $product
        ]);
    }
    
    public function approve($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->productRepo->update($id, ['is_active' => 1]);
        
        $_SESSION['flash_success'] = 'Produit approuvé';
        header('Location: /admin/produits');
        exit;
    }
    
    public function reject($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->productRepo->update($id, ['is_active' => 0]);
        
        $_SESSION['flash_success'] = 'Produit rejeté';
        header('Location: /admin/produits');
        exit;
    }
    
    public function toggleFeatured($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->productRepo->toggleFeatured($id);
        
        $_SESSION['flash_success'] = 'Statut mis à jour';
        header('Location: /admin/produits');
        exit;
    }
    
    public function destroy($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->productRepo->delete($id);
        
        $_SESSION['flash_success'] = 'Produit supprimé';
        header('Location: /admin/produits');
        exit;
    }
}