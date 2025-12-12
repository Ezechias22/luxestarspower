<?php
namespace App\Repositories;

use App\Database;
use App\Models\Order;

class OrderRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $result = $this->db->fetchOne("SELECT * FROM orders WHERE id = ?", [$id]);
        return $result ? new Order($result) : null;
    }
    
    public function findByOrderNumber($orderNumber) {
        $result = $this->db->fetchOne("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);
        return $result ? new Order($result) : null;
    }
    
    public function create($data) {
        $orderNumber = 'ORD-' . strtoupper(uniqid());
        $id = $this->db->insert(
            "INSERT INTO orders (order_number, buyer_id, seller_id, product_id, amount, seller_earnings, platform_fee, status, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$orderNumber, $data['buyer_id'], $data['seller_id'], $data['product_id'], $data['amount'], $data['seller_earnings'], $data['platform_fee'], $data['status'] ?? 'pending', $data['payment_method'] ?? null]
        );
        return $this->findById($id);
    }
    
    public function updateStatus($id, $status, $paymentReference = null) {
        if ($paymentReference) {
            return $this->db->query("UPDATE orders SET status = ?, payment_reference = ? WHERE id = ?", [$status, $paymentReference, $id]);
        }
        return $this->db->query("UPDATE orders SET status = ? WHERE id = ?", [$status, $id]);
    }
    
    public function getByBuyer($buyerId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $results = $this->db->fetchAll("SELECT o.*, p.title as product_title, p.thumbnail_path FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.buyer_id = ? ORDER BY o.created_at DESC LIMIT ? OFFSET ?", [$buyerId, $perPage, $offset]);
        return array_map(fn($row) => new Order($row), $results);
    }
    
    public function getBySeller($sellerId, $page = 1, $perPage = 20) {
        $offset = ($page - 1) * $perPage;
        $results = $this->db->fetchAll("SELECT o.*, p.title as product_title, u.name as buyer_name FROM orders o LEFT JOIN products p ON o.product_id = p.id LEFT JOIN users u ON o.buyer_id = u.id WHERE o.seller_id = ? ORDER BY o.created_at DESC LIMIT ? OFFSET ?", [$sellerId, $perPage, $offset]);
        return array_map(fn($row) => new Order($row), $results);
    }
    
    public function getAll($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT o.*, p.title as product_title, u.name as buyer_name, s.name as seller_name FROM orders o LEFT JOIN products p ON o.product_id = p.id LEFT JOIN users u ON o.buyer_id = u.id LEFT JOIN users s ON o.seller_id = s.id $whereClause ORDER BY o.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $results = $this->db->fetchAll($sql, $params);
        return array_map(fn($row) => new Order($row), $results);
    }
    
    public function getTotalRevenue() {
        $result = $this->db->fetchOne("SELECT SUM(platform_fee) as total FROM orders WHERE status = 'paid'");
        return floatval($result['total'] ?? 0);
    }
    
    public function getSellerEarnings($sellerId) {
        $result = $this->db->fetchOne("SELECT SUM(seller_earnings) as total FROM orders WHERE seller_id = ? AND status = 'paid'", [$sellerId]);
        return floatval($result['total'] ?? 0);
    }
}
