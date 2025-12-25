<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\CategoryRepository;

class CategoryController {
    private $auth;
    private $categoryRepo;

    public function __construct() {
        $this->auth = new AuthService();
        $this->categoryRepo = new CategoryRepository();
    }

    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $categories = $this->categoryRepo->getAll();

        view('admin/categories/index', [
            'user' => $user,
            'categories' => $categories
        ]);
    }

    public function store() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        try {
            $name = $_POST['name'] ?? '';
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $description = $_POST['description'] ?? '';
            $icon = $_POST['icon'] ?? '📦';
            $displayOrder = $_POST['display_order'] ?? 0;

            if (empty($name)) {
                throw new \Exception('Le nom est requis');
            }

            $this->categoryRepo->create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'icon' => $icon,
                'display_order' => $displayOrder
            ]);

            $_SESSION['flash_success'] = 'Catégorie créée avec succès';

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }
        
        header('Location: /admin/categories');
        exit;
    }

    public function update($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID catégorie invalide';
            header('Location: /admin/categories');
            exit;
        }

        try {
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'icon' => $_POST['icon'] ?? '',
                'display_order' => $_POST['display_order'] ?? 0,
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (!empty($data['name'])) {
                $data['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
            }

            $this->categoryRepo->update($id, $data);

            $_SESSION['flash_success'] = 'Catégorie mise à jour';

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /admin/categories');
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
            $_SESSION['flash_error'] = 'ID catégorie invalide';
            header('Location: /admin/categories');
            exit;
        }

        try {
            $this->categoryRepo->delete($id);
            $_SESSION['flash_success'] = 'Catégorie supprimée';

        } catch (\Exception $e) {
            $_SESSION['flash_error'] = 'Erreur : ' . $e->getMessage();
        }

        header('Location: /admin/categories');
        exit;
    }
}