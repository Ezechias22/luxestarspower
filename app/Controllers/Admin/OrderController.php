<?php
namespace App\Controllers\Admin;

use App\Services\AuthService;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;

class OrderController {
    private $auth;
    private $orderRepo;
    private $userRepo;

    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
        $this->userRepo = new UserRepository();
    }

    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $allOrders = $this->orderRepo->getAll();

        // Enrichit les commandes avec les infos utilisateur
        $orders = [];
        foreach ($allOrders as $order) {
            $buyer = $this->userRepo->findById($order['user_id']);
            $order['buyer_name'] = $buyer['name'] ?? 'Inconnu';
            $order['buyer_email'] = $buyer['email'] ?? '';
            $orders[] = $order;
        }

        view('admin/orders/index', [
            'user' => $user,
            'orders' => $orders
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
            die('Commande non trouvée');
        }

        $order = $this->orderRepo->getOrderWithItems($id);

        if (!$order) {
            http_response_code(404);
            die('Commande non trouvée');
        }

        $buyer = $this->userRepo->findById($order['user_id']);

        view('admin/orders/show', [
            'user' => $user,
            'order' => $order,
            'buyer' => $buyer
        ]);
    }

    public function refund($params) {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        $id = $params['id'] ?? null;

        if (!$id) {
            $_SESSION['flash_error'] = 'ID commande invalide';
            header('Location: /admin/commandes');
            exit;
        }

        // TODO: Implémenter le remboursement Stripe
        $this->orderRepo->update($id, ['payment_status' => 'refunded']);

        $_SESSION['flash_success'] = 'Remboursement effectué';
        header('Location: /admin/commandes');
        exit;
    }
}