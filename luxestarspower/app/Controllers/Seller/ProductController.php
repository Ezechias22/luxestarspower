<?php
namespace App\Controllers\Seller;

use App\Services\{AuthService, StorageService};
use App\Repositories\ProductRepository;

class ProductController {
    private $auth;
    private $storage;
    private $productRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->storage = new StorageService();
        $this->productRepo = new ProductRepository();
    }
    
    public function index() {
        $user = $this->auth->requireRole('seller');
        $page = $_GET['page'] ?? 1;
        
        $products = $this->productRepo->getAllPaginated($page, 20, ['seller_id' => $user->id]);
        
        return $this->render('seller/products/index', ['products' => $products]);
    }
    
    public function create() {
        $this->auth->requireRole('seller');
        return $this->render('seller/products/create');
    }
    
    public function store() {
        $user = $this->auth->requireRole('seller');
        
        $title = $_POST['title'] ?? '';
        $slug = $this->generateSlug($title);
        $description = $_POST['description'] ?? '';
        $type = $_POST['type'] ?? 'file';
        $price = floatval($_POST['price'] ?? 0);
        $currency = $_POST['currency'] ?? 'USD';
        
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['file']['tmp_name'];
            $filename = time() . '_' . basename($_FILES['file']['name']);
            $storagePath = "products/{$user->id}/$filename";
            
            $this->storage->uploadFile($tmpPath, $storagePath, 'private');
            
            $thumbnailPath = null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $thumbTmp = $_FILES['thumbnail']['tmp_name'];
                $thumbFilename = time() . '_thumb_' . basename($_FILES['thumbnail']['name']);
                $thumbPath = "products/{$user->id}/thumbs/$thumbFilename";
                $this->storage->uploadFile($thumbTmp, $thumbPath, 'public');
                $thumbnailPath = $thumbPath;
            }
            
            $this->productRepo->create([
                'seller_id' => $user->id,
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'type' => $type,
                'price' => $price,
                'currency' => $currency,
                'file_storage_path' => $storagePath,
                'thumbnail_path' => $thumbnailPath
            ]);
            
            header('Location: /vendeur/produits');
            exit;
        }
        
        return $this->render('seller/products/create', ['error' => 'File upload failed']);
    }
    
    public function edit($id) {
        $user = $this->auth->requireRole('seller');
        $product = $this->productRepo->findById($id);
        
        if (!$product || $product->seller_id != $user->id) {
            http_response_code(403);
            die('Forbidden');
        }
        
        return $this->render('seller/products/edit', ['product' => $product]);
    }
    
    public function update($id) {
        $user = $this->auth->requireRole('seller');
        $product = $this->productRepo->findById($id);
        
        if (!$product || $product->seller_id != $user->id) {
            http_response_code(403);
            die('Forbidden');
        }
        
        $this->productRepo->update($id, [
            'title' => $_POST['title'] ?? $product->title,
            'description' => $_POST['description'] ?? $product->description,
            'price' => $_POST['price'] ?? $product->price
        ]);
        
        header('Location: /vendeur/produits');
        exit;
    }
    
    private function generateSlug($title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return $slug . '-' . substr(uniqid(), -6);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
