<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\ProductRepository;

class SellerProductController {
    private $auth;
    private $productRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        $products = $this->productRepo->getBySeller($user['id']);
        
        view('seller/products/index', [
            'user' => $user,
            'products' => $products
        ]);
    }
    
    public function create() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        view('seller/products/create', [
            'user' => $user
        ]);
    }
    
    public function store() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        // Logique de création de produit à implémenter
        header('Location: /vendeur/produits');
        exit;
    }
    
    public function edit($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }
        
        $product = $this->productRepo->findById($id);
        
        if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
            http_response_code(404);
            die('Produit non trouvé');
        }
        
        view('seller/products/edit', [
            'user' => $user,
            'product' => $product
        ]);
    }
    
    public function update($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        // Logique de mise à jour à implémenter
        header('Location: /vendeur/produits');
        exit;
    }
    
    public function destroy($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        // Logique de suppression à implémenter
        header('Location: /vendeur/produits');
        exit;
    }
}