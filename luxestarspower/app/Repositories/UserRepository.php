<?php
namespace App\Repositories;

use App\Database;
use App\Models\User;

class UserRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function findById($id) {
        $result = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
        return $result ? new User($result) : null;
    }
    
    public function findByEmail($email) {
        $result = $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        return $result ? new User($result) : null;
    }
    
    public function create($data) {
        $id = $this->db->insert(
            "INSERT INTO users (name, email, password_hash, role, currency) VALUES (?, ?, ?, ?, ?)",
            [$data['name'], $data['email'], $data['password_hash'], $data['role'] ?? 'buyer', $data['currency'] ?? 'USD']
        );
        return $this->findById($id);
    }
    
    public function update($id, $data) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'bio', 'avatar_url', 'currency', 'settings'])) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        if (empty($fields)) return false;
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
        return $this->db->query($sql, $params);
    }
    
    public function updateRole($id, $role) {
        return $this->db->query("UPDATE users SET role = ? WHERE id = ?", [$role, $id]);
    }
    
    public function verifyEmail($id) {
        return $this->db->query("UPDATE users SET email_verified_at = NOW() WHERE id = ?", [$id]);
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
        
        $results = $this->db->fetchAll($sql, $params);
        return array_map(fn($row) => new User($row), $results);
    }
    
    public function count($filters = []) {
        $where = [];
        $params = [];
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users $whereClause", $params);
        return $result['cnt'] ?? 0;
    }
}
