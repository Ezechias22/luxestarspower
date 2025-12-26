<?php
namespace App\Repositories;

use App\Database;

class UserRepository {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Trouve un utilisateur par ID
     */
    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail($email) {
        return $this->db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    /**
     * Trouve un vendeur par shop_slug OU store_slug
     */
    public function findByShopSlug($slug) {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE (shop_slug = ? OR store_slug = ?) AND role = 'seller'",
            [$slug, $slug]
        );
    }
    
    /**
     * Alias pour findByShopSlug (compatibilité)
     */
    public function findByStoreSlug($slug) {
        return $this->findByShopSlug($slug);
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        $fields = ['name', 'email', 'password_hash', 'role', 'currency'];
        $values = [
            $data['name'], 
            $data['email'], 
            $data['password_hash'], 
            $data['role'] ?? 'buyer', 
            $data['currency'] ?? 'USD'
        ];
        
        // Ajoute shop_* ET store_* (synchronisés)
        if (isset($data['shop_name'])) {
            $fields[] = 'shop_name';
            $values[] = $data['shop_name'];
            $fields[] = 'store_name';
            $values[] = $data['shop_name'];
        }
        
        if (isset($data['shop_slug'])) {
            $fields[] = 'shop_slug';
            $values[] = $data['shop_slug'];
            $fields[] = 'store_slug';
            $values[] = $data['shop_slug'];
        }
        
        if (isset($data['shop_description'])) {
            $fields[] = 'shop_description';
            $values[] = $data['shop_description'];
            $fields[] = 'store_description';
            $values[] = $data['shop_description'];
        }
        
        if (isset($data['shop_logo'])) {
            $fields[] = 'shop_logo';
            $values[] = $data['shop_logo'];
            $fields[] = 'store_logo';
            $values[] = $data['shop_logo'];
        }
        
        if (isset($data['shop_banner'])) {
            $fields[] = 'shop_banner';
            $values[] = $data['shop_banner'];
            $fields[] = 'store_banner';
            $values[] = $data['shop_banner'];
        }
        
        // Si store_* est fourni sans shop_*, utilise-le
        if (isset($data['store_name']) && !isset($data['shop_name'])) {
            $fields[] = 'store_name';
            $values[] = $data['store_name'];
            $fields[] = 'shop_name';
            $values[] = $data['store_name'];
        }
        
        if (isset($data['store_slug']) && !isset($data['shop_slug'])) {
            $fields[] = 'store_slug';
            $values[] = $data['store_slug'];
            $fields[] = 'shop_slug';
            $values[] = $data['store_slug'];
        }
        
        if (isset($data['store_description']) && !isset($data['shop_description'])) {
            $fields[] = 'store_description';
            $values[] = $data['store_description'];
            $fields[] = 'shop_description';
            $values[] = $data['store_description'];
        }
        
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        
        $id = $this->db->insert(
            "INSERT INTO users (" . implode(', ', $fields) . ", created_at) VALUES ($placeholders, NOW())",
            $values
        );
        
        return $this->findById($id);
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'name', 'bio', 'avatar_url', 'currency', 'settings', 'role', 
            'shop_name', 'shop_slug', 'shop_description', 'shop_logo', 'shop_banner',
            'store_name', 'store_slug', 'store_description', 'store_logo', 'store_banner',
            'last_login_at', 'password_hash', 'email_verified_at', 'is_suspended',
            'password_reset_token', 'password_reset_expires', 'email'
        ];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "$key = ?";
                $params[] = $value;
                
                // Synchronise automatiquement shop_ <-> store_
                if (strpos($key, 'shop_') === 0) {
                    $storeKey = str_replace('shop_', 'store_', $key);
                    if (in_array($storeKey, $allowedFields) && !isset($data[$storeKey])) {
                        $fields[] = "$storeKey = ?";
                        $params[] = $value;
                    }
                } elseif (strpos($key, 'store_') === 0) {
                    $shopKey = str_replace('store_', 'shop_', $key);
                    if (in_array($shopKey, $allowedFields) && !isset($data[$shopKey])) {
                        $fields[] = "$shopKey = ?";
                        $params[] = $value;
                    }
                }
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Met à jour le rôle d'un utilisateur
     */
    public function updateRole($id, $role) {
        return $this->db->query(
            "UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?", 
            [$role, $id]
        );
    }
    
    /**
     * Vérifie l'email d'un utilisateur
     */
    public function verifyEmail($id) {
        return $this->db->query(
            "UPDATE users SET email_verified_at = NOW(), updated_at = NOW() WHERE id = ?", 
            [$id]
        );
    }
    
    /**
     * Trouve un utilisateur par token de réinitialisation
     */
    public function findByPasswordResetToken($token) {
        return $this->db->fetchOne(
            "SELECT * FROM users WHERE password_reset_token = ?",
            [$token]
        );
    }
    
    /**
     * Récupère tous les utilisateurs avec pagination
     */
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
        
        if (isset($filters['is_suspended'])) {
            $where[] = "is_suspended = ?";
            $params[] = $filters['is_suspended'];
        }
        
        if (!empty($filters['email_verified'])) {
            $where[] = "email_verified_at IS NOT NULL";
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $sql = "SELECT * FROM users $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Compte le nombre total d'utilisateurs
     */
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
        
        if (isset($filters['is_suspended'])) {
            $where[] = "is_suspended = ?";
            $params[] = $filters['is_suspended'];
        }
        
        if (!empty($filters['email_verified'])) {
            $where[] = "email_verified_at IS NOT NULL";
        }
        
        $whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users $whereClause", $params);
        
        return $result['cnt'] ?? 0;
    }
    
    /**
     * Récupère les statistiques d'un vendeur
     */
    public function getSellerStats($sellerId) {
        $result = $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT o.id) as total_sales,
                COALESCE(SUM(o.amount), 0) as total_revenue
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
    
    /**
     * Récupère tous les vendeurs
     */
    public function getAllSellers($limit = null) {
        $sql = "SELECT * FROM users WHERE role = 'seller' ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Compte le nombre de vendeurs
     */
    public function countSellers() {
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE role = 'seller'");
        return $result['cnt'] ?? 0;
    }
    
    /**
     * Compte le nombre d'acheteurs
     */
    public function countBuyers() {
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE role = 'buyer'");
        return $result['cnt'] ?? 0;
    }
    
    /**
     * Récupère les vendeurs les plus actifs
     */
    public function getTopSellers($limit = 10) {
        return $this->db->fetchAll(
            "SELECT u.*, COUNT(DISTINCT o.id) as total_orders
             FROM users u
             LEFT JOIN products p ON u.id = p.seller_id
             LEFT JOIN order_items oi ON p.id = oi.product_id
             LEFT JOIN orders o ON oi.order_id = o.id AND o.status = 'completed'
             WHERE u.role = 'seller'
             GROUP BY u.id
             ORDER BY total_orders DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Suspend un utilisateur
     */
    public function suspend($id, $reason = null) {
        return $this->db->query(
            "UPDATE users SET is_suspended = 1, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Réactive un utilisateur
     */
    public function unsuspend($id) {
        return $this->db->query(
            "UPDATE users SET is_suspended = 0, updated_at = NOW() WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Supprime un utilisateur (soft delete possible)
     */
    public function delete($id) {
        return $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as cnt FROM users WHERE email = ? AND id != ?",
                [$email, $excludeId]
            );
        } else {
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as cnt FROM users WHERE email = ?",
                [$email]
            );
        }
        
        return ($result['cnt'] ?? 0) > 0;
    }
    
    /**
     * Vérifie si un shop_slug existe déjà
     */
    public function shopSlugExists($slug, $excludeId = null) {
        if ($excludeId) {
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as cnt FROM users WHERE (shop_slug = ? OR store_slug = ?) AND id != ?",
                [$slug, $slug, $excludeId]
            );
        } else {
            $result = $this->db->fetchOne(
                "SELECT COUNT(*) as cnt FROM users WHERE (shop_slug = ? OR store_slug = ?)",
                [$slug, $slug]
            );
        }
        
        return ($result['cnt'] ?? 0) > 0;
    }
    
    /**
     * Met à jour la date de dernière connexion
     */
    public function updateLastLogin($id) {
        return $this->db->query(
            "UPDATE users SET last_login_at = NOW() WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Recherche des utilisateurs
     */
    public function search($query, $limit = 20) {
        $search = '%' . $query . '%';
        return $this->db->fetchAll(
            "SELECT * FROM users 
             WHERE name LIKE ? OR email LIKE ? OR shop_name LIKE ? OR store_name LIKE ?
             LIMIT ?",
            [$search, $search, $search, $search, $limit]
        );
    }
    
    /**
     * Récupère les utilisateurs récents
     */
    public function getRecentUsers($limit = 10) {
        return $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Récupère le nombre total d'utilisateurs
     */
    public function getTotalUsers() {
        $result = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users");
        return $result['cnt'] ?? 0;
    }
}