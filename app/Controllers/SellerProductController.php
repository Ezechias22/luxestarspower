<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Services\StorageService;
use App\Repositories\ProductRepository;

class SellerProductController {
    private $auth;
    private $productRepo;
    private $storage;

    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
        $this->storage = new StorageService();
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
            $title = $_POST['title'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $type = $_POST['type'] ?? '';
            $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

            // Validation
            if (empty($title) || empty($description) || empty($type)) {
                throw new \Exception('Tous les champs requis doivent être remplis');
            }

            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Le fichier du produit est requis');
            }

            // Génère un slug unique
            $slug = $this->generateUniqueSlug($title);

            // Upload du fichier principal vers Cloudinary
            $fileUrl = $this->storage->uploadFile($_FILES['file']['tmp_name'], 'products');

            // Upload de la miniature si présente, sinon null
            $thumbnailUrl = null;
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $thumbnailUrl = $this->storage->uploadImage($_FILES['thumbnail']['tmp_name'], 'thumbnails');
            }
            // Si pas d'image uploadée, on laisse null et le frontend affichera l'emoji par défaut

            // Crée le produit
            $product = $this->productRepo->create([
                'seller_id' => $user['id'],
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'type' => $type,
                'price' => $price,
                'currency' => 'USD',
                'file_storage_path' => $fileUrl,
                'thumbnail_path' => $thumbnailUrl, // Peut être null
                'is_featured' => $isFeatured,
                'is_active' => 1
            ]);

            $_SESSION['flash_success'] = 'Produit ajouté avec succès ! 🎉';

        } catch (\Exception $e) {
            error_log("Product creation error: " . $e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /vendeur/produits/nouveau');
            exit;
        }

        header('Location: /vendeur/produits');
        exit;
    }

    public function edit($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            die('Produit non trouvé');
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

    public function update($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /vendeur/produits');
            exit;
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
                'type' => $_POST['type'] ?? $product['type'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0
            ];

            // Upload nouvelle miniature si fournie
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $newThumbnailUrl = $this->storage->uploadImage($_FILES['thumbnail']['tmp_name'], 'thumbnails');
                $data['thumbnail_path'] = $newThumbnailUrl;

                // Supprime l'ancienne miniature de Cloudinary si elle existe
                if (!empty($product['thumbnail_path']) && strpos($product['thumbnail_path'], 'cloudinary.com') !== false) {
                    try {
                        $this->storage->deleteFile($product['thumbnail_path']);
                    } catch (\Exception $e) {
                        // Ignore l'erreur de suppression
                    }
                }
            }

            $this->productRepo->update($id, $data);

            $_SESSION['flash_success'] = 'Produit mis à jour avec succès ! ✅';

        } catch (\Exception $e) {
            error_log("Product update error: " . $e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
        }

        header('Location: /vendeur/produits');
        exit;
    }

    public function destroy($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;
        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /vendeur/produits');
            exit;
        }

        try {
            $product = $this->productRepo->findById($id);

            if (!$product || ($product['seller_id'] != $user['id'] && $user['role'] !== 'admin')) {
                throw new \Exception('Produit non trouvé');
            }

            // Supprime les fichiers de Cloudinary
            if (!empty($product['file_storage_path']) && strpos($product['file_storage_path'], 'cloudinary.com') !== false) {
                try {
                    $this->storage->deleteFile($product['file_storage_path']);
                } catch (\Exception $e) {
                    // Ignore l'erreur
                }
            }

            if (!empty($product['thumbnail_path']) && strpos($product['thumbnail_path'], 'cloudinary.com') !== false) {
                try {
                    $this->storage->deleteFile($product['thumbnail_path']);
                } catch (\Exception $e) {
                    // Ignore l'erreur
                }
            }

            $this->productRepo->delete($id);

            $_SESSION['flash_success'] = 'Produit supprimé avec succès ! 🗑️';

        } catch (\Exception $e) {
            error_log("Product deletion error: " . $e->getMessage());
            $_SESSION['flash_error'] = $e->getMessage();
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