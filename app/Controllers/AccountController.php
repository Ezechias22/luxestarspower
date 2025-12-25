<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\OrderRepository;

class AccountController {
    private $auth;
    private $orderRepo;

    public function __construct() {
        $this->auth = new AuthService();
        $this->orderRepo = new OrderRepository();
    }

    public function dashboard() {
        $user = $this->auth->requireAuth();

        // Récupérer les statistiques
        $orders = $this->orderRepo->getByUser($user['id']);
        $totalOrders = count($orders);
        
        $totalSpent = 0;
        foreach ($orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $totalSpent += $order['total_amount'];
            }
        }

        view('account/dashboard', [
            'user' => $user,
            'totalOrders' => $totalOrders,
            'totalSpent' => $totalSpent
        ]);
    }

    public function purchases($params = []) {
        $user = $this->auth->requireAuth();
        $page = $_GET['page'] ?? 1;

        $orders = $this->orderRepo->getByBuyer($user['id'], $page);

        view('account/purchases', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    public function downloads($params = []) {
        $user = $this->auth->requireAuth();
        
        // Récupère toutes les commandes payées
        $orders = $this->orderRepo->getByUser($user['id']);
        $downloads = [];
        
        foreach ($orders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                foreach ($items as $item) {
                    $downloads[] = [
                        'order_id' => $order['id'],
                        'product_title' => $item['title'],
                        'product_type' => $item['type'],
                        'file_path' => $item['file_storage_path'],
                        'purchased_at' => $order['created_at']
                    ];
                }
            }
        }

        view('account/downloads', [
            'user' => $user,
            'downloads' => $downloads
        ]);
    }

    public function settings($params = []) {
        $user = $this->auth->requireAuth();

        view('account/settings', [
            'user' => $user
        ]);
    }

    public function updateSettings() {
        $user = $this->auth->requireAuth();
        $userRepo = new \App\Repositories\UserRepository();

        $userRepo->update($user['id'], [
            'name' => $_POST['name'] ?? $user['name']
        ]);

        $_SESSION['user_name'] = $_POST['name'] ?? $user['name'];

        header('Location: /compte/parametres?success=1');
        exit;
    }
}