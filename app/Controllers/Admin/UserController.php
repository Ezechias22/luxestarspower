<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\UserRepository;

class UserController {
    private $auth;
    private $userRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->userRepo = new UserRepository();
    }
    
    public function index() {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        
        $filters = [];
        if ($role) $filters['role'] = $role;
        if ($search) $filters['search'] = $search;
        
        $users = $this->userRepo->getAllPaginated($page, 20, $filters);
        
        view('admin/users/index', [
            'user' => $user,
            'users' => $users,
            'currentPage' => $page,
            'filters' => $filters
        ]);
    }
    
    public function show($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $targetUser = $this->userRepo->findById($id);
        
        if (!$targetUser) {
            http_response_code(404);
            die('Utilisateur non trouvé');
        }
        
        view('admin/users/show', [
            'user' => $user,
            'targetUser' => $targetUser
        ]);
    }
    
    public function suspend($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->userRepo->update($id, ['is_active' => 0]);
        
        $_SESSION['flash_success'] = 'Utilisateur suspendu';
        header('Location: /admin/utilisateurs');
        exit;
    }
    
    public function activate($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $this->userRepo->update($id, ['is_active' => 1]);
        
        $_SESSION['flash_success'] = 'Utilisateur activé';
        header('Location: /admin/utilisateurs');
        exit;
    }
    
    public function updateRole($id) {
        $user = $this->auth->requireAuth();
        
        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }
        
        $newRole = $_POST['role'] ?? '';
        
        if (!in_array($newRole, ['buyer', 'seller', 'admin'])) {
            http_response_code(400);
            die('Rôle invalide');
        }
        
        $this->userRepo->updateRole($id, $newRole);
        
        $_SESSION['flash_success'] = 'Rôle mis à jour';
        header('Location: /admin/utilisateurs');
        exit;
    }
}