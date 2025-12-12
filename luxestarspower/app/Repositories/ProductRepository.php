<?php
namespace App\Repositories;

use App\Database;
use App\Models\Product;

class ProductRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $result = $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
        return $result ? new Product($result) : null;
    }
    
    public function findBySlug($slug) {
        $result = $this->db->fetchOne("SELECT * FROM products WHERE slug = ? AND is_active = 1", [$slug]);
        return $result ? new Product($result) : null;
    }
    
    public function create($data) {
        $id = $this->db->insert(
            "INSERT INTO products (seller_id, title, slug, description, type, price, currency, file_storage_path, thumbnail_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$data['seller_id'], $data['title'], $data['slug'], $data['description'], $data['type'], $data['price'], $data['currency'], $data['file_storage_path'], $data['thumbnail_path'] ?? null]
        );
        return $this->findById($id);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        $allowed = ['title', 'slug', 'description', 'price', 'is_active', 'thumbnail_path'];
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        return $this->db->query("UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?", $params);
    }
    
    public function incrementViews($id) {
        return $this->db->query("UPDATE products SET views = views + 1 WHERE id = ?", [$id]);
    }
    
    public function incrementSales($id) {
        return $this->db->query("UPDATE products SET sales = sales + 1 WHERE id = ?", [$id]);
    }
    
    public function getAllPaginated($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = ['is_active = 1'];
        $params = [];
        
        if (!empty($filters['type'])) {
            $where[] = "type = ?";
            $params[] = $filters['type'];
        }
        if (!empty($filters['seller_id'])) {
            $where[] = "seller_id = ?";
            $params[] = $filters['seller_id'];
        }
        if (!empty($filters['search'])) {
            $where[] = "MATCH(title, description) AGAINST(? IN BOOLEAN MODE)";
            $params[] = $filters['search'];
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $orderBy = $filters['sort'] ?? 'created_at DESC';
        $sql = "SELECT * FROM products $whereClause ORDER BY $orderBy LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $results = $this->db->fetchAll($sql, $params);
        return array_map(fn($row) => new Product($row), $results);
    }
    
    public function getFeatured($limit = 10) {
        $results = $this->db->fetchAll("SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC LIMIT ?", [$limit]);
        return array_map(fn($row) => new Product($row), $results);
    }
    
    public function toggleFeatured($id) {
        return $this->db->query("UPDATE products SET is_featured = NOT is_featured WHERE id = ?", [$id]);
    }
    
    public function toggleActive($id) {
        return $this->db->query("UPDATE products SET is_active = NOT is_active WHERE id = ?", [$id]);
    }
}
