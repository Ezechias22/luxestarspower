<?php
namespace App\Repositories;

use App\Database;

class UserRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    public function findByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    public function create($data) {
        $id = $this->db->insert(
            "INSERT INTO users (name, email, password_hash, role, currency, created_at) VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $data['name'], 
                $data['email'], 
                $data['password_hash'], 
                $data['role'] ?? 'buyer', 
                $data['currency'] ?? 'USD'
            ]
        );
        
        return $this->findById($id);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'bio', 'avatar_url', 'currency', 'settings', 'role'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        return $this->db->query($sql, $params);
    }
    
    public function updateRole($id, $role) {
        return $this->db->query(
            "UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?", 
            [$role, $id]
        );
    }
    
    public function verifyEmail($id) {
        return $this->db->query(
            "UPDATE users SET email_verified_at = NOW(), updated_at = NOW() WHERE id = ?", 
            [$id]
        );
    }
    
    public function getAllPaginated($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = [];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT * FROM users $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function count($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(name LIKE ? OR email LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users $whereClause", $params);
        
        return $result['cnt'] ?? 0;
    }
    
    public function getSellerStats($sellerId) {
        $result = $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT o.id) as total_sales,
                SUM(o.amount) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE p.seller_id = ? AND o.status = 'completed'",
            [$sellerId]
        );
        
        return [
            'total_sales' => $result['total_sales'] ?? 0,
            'total_revenue' => $result['total_revenue'] ?? 0
        ];
    }
}