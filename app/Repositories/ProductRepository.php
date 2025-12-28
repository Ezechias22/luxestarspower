<?php
namespace App\Repositories;

use App\Database;

class ProductRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findById($id) {
        return $this->db->fetchOne("SELECT * FROM products WHERE id = ?", [$id]);
    }

    public function findBySlug($slug) {
        return $this->db->fetchOne("SELECT * FROM products WHERE slug = ? AND is_active = 1", [$slug]);
    }

    /**
     * CORRECTION: Pas besoin de CAST - seller_id est bigint
     */
    public function getBySeller($sellerId) {
        $sellerIdInt = (int)$sellerId;
        
        return $this->db->fetchAll(
            "SELECT * FROM products 
             WHERE seller_id = ? 
             ORDER BY created_at DESC",
            [$sellerIdInt]
        );
    }

    public function create($data) {
        $id = $this->db->insert(
            "INSERT INTO products (seller_id, title, slug, description, type, price, currency, file_storage_path, thumbnail_path, is_featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $data['seller_id'],
                $data['title'],
                $data['slug'],
                $data['description'],
                $data['type'],
                $data['price'],
                $data['currency'] ?? 'USD',
                $data['file_storage_path'],
                $data['thumbnail_path'] ?? null,
                $data['is_featured'] ?? 0
            ]
        );

        return $this->findById($id);
    }

    public function update($id, $data) {
        $fields = [];
        $params = [];
        $allowed = ['title', 'slug', 'description', 'price', 'is_active', 'is_featured', 'thumbnail_path', 'type'];

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
            "UPDATE products SET " . implode(', ', $fields) . " WHERE id = ?",
            $params
        );
    }

    public function delete($id) {
        return $this->db->query("DELETE FROM products WHERE id = ?", [$id]);
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
            $sellerIdInt = (int)$filters['seller_id'];
            $where[] = "seller_id = ?";
            $params[] = $sellerIdInt;
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR description LIKE ?)";
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where);
        $orderBy = $filters['sort'] ?? 'created_at DESC';

        $sql = "SELECT * FROM products $whereClause ORDER BY $orderBy LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $results = $this->db->fetchAll($sql, $params);

        // Compte total
        $countSql = "SELECT COUNT(*) as total FROM products $whereClause";
        $countParams = array_slice($params, 0, count($params) - 2);
        $total = $this->db->fetchOne($countSql, $countParams)['total'] ?? 0;

        return [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    public function getFeatured($limit = 10) {
        return $this->db->fetchAll(
            "SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }

    public function toggleFeatured($id) {
        return $this->db->query("UPDATE products SET is_featured = NOT is_featured WHERE id = ?", [$id]);
    }

    public function toggleActive($id) {
        return $this->db->query("UPDATE products SET is_active = NOT is_active WHERE id = ?", [$id]);
    }

    /**
     * Récupère le total de vues pour un vendeur
     */
    public function getTotalViewsBySeller($sellerId) {
        $sellerIdInt = (int)$sellerId;
        
        $result = $this->db->fetchOne(
            "SELECT SUM(views) as total_views 
             FROM products 
             WHERE seller_id = ?",
            [$sellerIdInt]
        );
        
        return $result['total_views'] ?? 0;
    }
    
    /**
     * Compte le nombre de produits d'un vendeur
     */
    public function countBySeller($sellerId) {
        $sellerIdInt = (int)$sellerId;
        
        $result = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt 
             FROM products 
             WHERE seller_id = ?",
            [$sellerIdInt]
        );
        
        return $result['cnt'] ?? 0;
    }
    
    /**
     * Récupère tous les produits actifs
     */
    public function getAll($limit = null) {
        $sql = "SELECT p.*, u.name as seller_name, u.shop_slug 
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                WHERE p.is_active = 1
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->fetchAll($sql, [$limit]);
        }
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Recherche des produits
     */
    public function search($query, $limit = 20) {
        $search = '%' . $query . '%';
        
        return $this->db->fetchAll(
            "SELECT p.*, u.name as seller_name, u.shop_slug 
             FROM products p
             LEFT JOIN users u ON p.seller_id = u.id
             WHERE p.is_active = 1
             AND (p.title LIKE ? OR p.description LIKE ?)
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$search, $search, $limit]
        );
    }
    
    /**
     * Récupère les produits récents
     */
    public function getRecent($limit = 10) {
        return $this->db->fetchAll(
            "SELECT p.*, u.name as seller_name, u.shop_slug 
             FROM products p
             LEFT JOIN users u ON p.seller_id = u.id
             WHERE p.is_active = 1
             ORDER BY p.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Récupère les statistiques de ventes d'un produit
     */
    public function getSalesStats($productId) {
        $result = $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT oi.order_id) as total_sales,
                SUM(oi.quantity) as total_quantity,
                SUM(oi.price * oi.quantity) as total_revenue
             FROM order_items oi
             JOIN orders o ON oi.order_id = o.id
             WHERE oi.product_id = ? AND o.status = 'completed'",
            [$productId]
        );
        
        return [
            'total_sales' => $result['total_sales'] ?? 0,
            'total_quantity' => $result['total_quantity'] ?? 0,
            'total_revenue' => $result['total_revenue'] ?? 0
        ];
    }
}