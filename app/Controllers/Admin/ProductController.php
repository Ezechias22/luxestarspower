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

    public function show($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;
        
        if (!$id) {
            http_response_code(404);
            die('Produit non trouvé');
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

    public function approve($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /admin/produits');
            exit;
        }

        $this->productRepo->update($id, ['is_active' => 1]);

        $_SESSION['flash_success'] = 'Produit approuvé';
        header('Location: /admin/produits');
        exit;
    }

    public function reject($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /admin/produits');
            exit;
        }

        $this->productRepo->update($id, ['is_active' => 0]);
        
        $_SESSION['flash_success'] = 'Produit rejeté';
        header('Location: /admin/produits');
        exit;
    }

    public function toggleFeatured($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /admin/produits');
            exit;
        }

        $this->productRepo->toggleFeatured($id);

        $_SESSION['flash_success'] = 'Statut mis à jour';
        header('Location: /admin/produits');
        exit;
    }

    public function destroy($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID produit invalide';
            header('Location: /admin/produits');
            exit;
        }

        $this->productRepo->delete($id);

        $_SESSION['flash_success'] = 'Produit supprimé';
        header('Location: /admin/produits');
        exit;
    }
}