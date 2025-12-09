<?php

namespace App\Controllers;

class HomeController
{
    private $db;
    
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }
    
    public function index()
    {
        // Get featured products
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as seller_name,
                   (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND is_approved = 1) as avg_rating,
                   (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND is_approved = 1) as review_count
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.is_active = 1 AND p.is_featured = 1
            ORDER BY p.created_at DESC
            LIMIT 12
        ");
        $stmt->execute();
        $featuredProducts = $stmt->fetchAll();
        
        // Get latest products
        $stmt = $this->db->prepare("
            SELECT p.*, u.name as seller_name
            FROM products p
            JOIN users u ON p.seller_id = u.id
            WHERE p.is_active = 1
            ORDER BY p.created_at DESC
            LIMIT 12
        ");
        $stmt->execute();
        $latestProducts = $stmt->fetchAll();
        
        // Get categories
        $stmt = $this->db->prepare("
            SELECT * FROM categories
            WHERE is_active = 1 AND parent_id IS NULL
            ORDER BY display_order, name
        ");
        $stmt->execute();
        $categories = $stmt->fetchAll();
        
        // Get stats for homepage
        $stats = [
            'total_products' => $this->getCount('products', 'is_active = 1'),
            'total_sellers' => $this->getCount('users', "role = 'seller' AND is_active = 1"),
            'total_sales' => $this->getCount('orders', "status = 'paid'"),
        ];
        
        echo view('front/home', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories,
            'stats' => $stats,
        ]);
    }
    
    private function getCount($table, $where = '1=1')
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$table} WHERE {$where}");
        $result = $stmt->fetch();
        return $result['count'];
    }
}
