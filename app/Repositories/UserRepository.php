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
    
    public function findByShopSlug($slug) {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE shop_slug = ? AND role = 'seller'",
            [$slug]
        );
    }
    
    public function create($data) {
        $fields = ['name', 'email', 'password_hash', 'role', 'currency'];
        $values = [
            $data['name'], 
            $data['email'], 
            $data['password_hash'], 
            $data['role'] ?? 'buyer', 
            $data['currency'] ?? 'USD'
        ];
        
        // Ajoute les champs boutique si présents
        if (isset($data['shop_name'])) {
            $fields[] = 'shop_name';
            $values[] = $data['shop_name'];
        }
        if (isset($data['shop_slug'])) {
            $fields[] = 'shop_slug';
            $values[] = $data['shop_slug'];
        }
        if (isset($data['shop_description'])) {
            $fields[] = 'shop_description';
            $values[] = $data['shop_description'];
        }
        
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        
        $id = $this->db->insert(
            "INSERT INTO users (" . implode(', ', $fields) . ", created_at) VALUES ($placeholders, NOW())",
            $values
        );
        
        return $this->findById($id);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = ['name', 'bio', 'avatar_url', 'currency', 'settings', 'role', 'shop_name', 'shop_slug', 'shop_description', 'shop_logo', 'shop_banner'];
        
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