<?php
namespace App\Repositories;

use App\Database;

class OrderRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $orderId = $this->db->insert(
            "INSERT INTO orders (user_id, total_amount, status, payment_method, payment_status, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['user_id'],
                $data['total_amount'],
                $data['status'] ?? 'pending',
                $data['payment_method'] ?? 'stripe',
                $data['payment_status'] ?? 'pending'
            ]
        );
        
        return $this->findById($orderId);
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        $allowed = ['status', 'payment_status', 'payment_method'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $fields[] = "updated_at = NOW()";
        $params[] = $id;
        
        return $this->db->query(
            "UPDATE orders SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        );
    }
    
    public function addOrderItem($orderId, $productId, $sellerId, $price, $quantity = 1) {
        return $this->db->insert(
            "INSERT INTO order_items (order_id, product_id, seller_id, price, quantity, created_at) 
             VALUES (?, ?, ?, ?, ?, NOW())",
            [$orderId, $productId, $sellerId, $price, $quantity]
        );
    }
    
    public function getOrderItems($orderId) {
        return $this->db->fetchAll(
            "SELECT oi.*, p.title, p.type, p.file_storage_path 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }
    
    public function getByUser($userId) {
        return $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC",
            [$userId]
        );
    }

    /**
     * Récupère toutes les commandes (pour les statistiques vendeur)
     */
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM orders ORDER BY created_at DESC"
        );
    }

    public function getByBuyer($userId, $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;

        return $this->db->fetchAll(
            "SELECT o.*, 
                    COUNT(oi.id) as item_count,
                    GROUP_CONCAT(p.title SEPARATOR ', ') as products
             FROM orders o
             LEFT JOIN order_items oi ON o.id = oi.order_id
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE o.user_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
    }

    public function getOrderWithItems($orderId) {
        $order = $this->findById($orderId);
        if ($order) {
            $order['items'] = $this->getOrderItems($orderId);
        }
        return $order;
    }
}