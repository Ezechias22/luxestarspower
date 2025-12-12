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
        $this->auth->requireRole('admin');
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        
        $filters = [];
        if ($search) $filters['search'] = $search;
        
        $users = $this->userRepo->getAllPaginated($page, 20, $filters);
        
        return $this->render('admin/users/index', ['users' => $users]);
    }
    
    private function render($view, $data = []) {
        extract($data);
        ob_start();
        require __DIR__ . '/../../../views/' . $view . '.php';
        return ob_get_clean();
    }
}
