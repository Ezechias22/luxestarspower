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
        
        try {
            // Validation
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $type = $_POST['type'] ?? '';
            
            if (empty($title) || empty($description) || empty($type)) {
                throw new \Exception('Tous les champs sont requis');
            }
            
            // Génère le slug
            $slug = $this->generateUniqueSlug($title);
            
            // Upload de fichier (temporaire - stockage local)
            $filePath = '/uploads/products/' . uniqid() . '_' . ($_FILES['file']['name'] ?? 'file.pdf');
            $thumbnailPath = null;
            
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $thumbnailPath = '/uploads/thumbnails/' . uniqid() . '_' . $_FILES['thumbnail']['name'];
            }
            
            // Crée le produit
            $product = $this->productRepo->create([
                'seller_id' => $user['id'],
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'type' => $type,
                'price' => $price,
                'currency' => 'EUR',
                'file_storage_path' => $filePath,
                'thumbnail_path' => $thumbnailPath
            ]);
            
            $_SESSION['flash_success'] = 'Produit ajouté avec succès !';
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
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
        
        try {
            $product = $this->productRepo->findById($id);
            
            if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new \Exception('Produit non trouvé');
            }
            
            $data = [
                'title' => $_POST['title'] ?? $product['title'],
                'description' => $_POST['description'] ?? $product['description'],
                'price' => $_POST['price'] ?? $product['price'],
                'type' => $_POST['type'] ?? $product['type']
            ];
            
            $this->productRepo->update($id, $data);
            
            $_SESSION['flash_success'] = 'Produit mis à jour avec succès !';
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: /vendeur/produits');
        exit;
    }
    
    public function destroy($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        try {
            $product = $this->productRepo->findById($id);
            
            if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new \Exception('Produit non trouvé');
            }
            
            $this->productRepo->delete($id);
            
            $_SESSION['flash_success'] = 'Produit supprimé avec succès !';
            
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: /vendeur/produits');
        exit;
    }
    
    private function generateUniqueSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->productRepo->findBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}