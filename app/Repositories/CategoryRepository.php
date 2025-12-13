<?php
namespace App\Repositories;

use App\Database;

class CategoryRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]);
    }
    
    public function findBySlug($slug) {
        return $this->db->fetchOne("SELECT * FROM categories WHERE slug = ?", [$slug]);
    }
    
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY display_order ASC, name ASC");
    }
    
    public function getActive() {
        return $this->db->fetchAll("SELECT * FROM categories WHERE is_active = 1 ORDER BY display_order ASC, name ASC");
    }
    
    public function create($data) {
        $id = $this->db->insert(
            "INSERT INTO categories (name, slug, description, icon, display_order) VALUES (?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? null,
                $data['icon'] ?? '📦',
                $data['display_order'] ?? 0
            ]
        );
        
        return $this->findById($id);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        $allowed = ['name', 'slug', 'description', 'icon', 'is_active', 'display_order'];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        
        return $this->db->query(
            "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        );
    }
    
    public function delete($id) {
        return $this->db->query("DELETE FROM categories WHERE id = ?", [$id]);
    }
    
    public function count($filters = []) {
        $where = [];
        $params = [];
        
        if (isset($filters['is_active'])) {
            $where[] = "is_active = ?";
            $params[] = $filters['is_active'];
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        $result = $this->db->fetchOne("SELECT COUNT(*) as total FROM categories $whereClause", $params);
        return $result['total'] ?? 0;
    }
}