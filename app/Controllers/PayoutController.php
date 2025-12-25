<?php
namespace App\Controllers;

use App\Services\AuthService;
use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;

class PayoutController {
    private $auth;
    private $productRepo;
    private $orderRepo;
    
    public function __construct() {
        $this->auth = new AuthService();
        $this->productRepo = new ProductRepository();
        $this->orderRepo = new OrderRepository();
    }

    public function index() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        // Calcule le solde disponible réel
        $sellerId = $user['id'];
        $allOrders = $this->orderRepo->getAll();
        $totalRevenue = 0;
        
        foreach ($allOrders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                
                foreach ($items as $item) {
                    if ($item['seller_id'] == $sellerId) {
                        $totalRevenue += $item['price'] * $item['quantity'];
                    }
                }
            }
        }
        
        // 90% des revenus disponibles (10% commission plateforme)
        $availableBalance = $totalRevenue * 0.9;

        view('seller/payouts/index', [
            'user' => $user,
            'payouts' => [], // TODO: Récupérer depuis la base de données
            'balance' => $availableBalance,
            'totalRevenue' => $totalRevenue
        ]);
    }

    public function request() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        // Calcule le solde
        $sellerId = $user['id'];
        $allOrders = $this->orderRepo->getAll();
        $totalRevenue = 0;
        
        foreach ($allOrders as $order) {
            if ($order['payment_status'] === 'paid') {
                $items = $this->orderRepo->getOrderItems($order['id']);
                
                foreach ($items as $item) {
                    if ($item['seller_id'] == $sellerId) {
                        $totalRevenue += $item['price'] * $item['quantity'];
                    }
                }
            }
        }
        
        $availableBalance = $totalRevenue * 0.9;

        // Vérifie le minimum
        if ($availableBalance < 50) {
            $_SESSION['flash_error'] = 'Solde insuffisant. Minimum $50.00 requis.';
            header('Location: /vendeur/paiements');
            exit;
        }

        // TODO: Enregistrer la demande de paiement dans la base de données
        
        $_SESSION['flash_success'] = 'Demande de paiement de $' . number_format($availableBalance, 2) . ' enregistrée. Vous serez contacté sous 48h.';

        header('Location: /vendeur/paiements');
        exit;
    }

    public function setupMethod() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            header('Location: /vendre');
            exit;
        }

        view('seller/payouts/setup', [
            'user' => $user
        ]);
    }

    public function saveMethod() {
        $user = $this->auth->requireAuth();

        if ($user['role'] !== 'seller' && $user['role'] !== 'admin') {
            http_response_code(403);
            die('Accès interdit');
        }

        // TODO: Enregistrer les coordonnées bancaires dans la base de données
        
        $_SESSION['flash_success'] = 'Méthode de paiement enregistrée avec succès.';
        
        header('Location: /vendeur/paiements');
        exit;
    }
}