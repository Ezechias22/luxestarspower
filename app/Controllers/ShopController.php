<?php
namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\ProductRepository;
use App\Database;

class ShopController {
    private $userRepo;
    private $productRepo;
    private $db;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->productRepo = new ProductRepository();
        $this->db = Database::getInstance();
    }
    
    public function show($params) {
        $slug = $params['slug'] ?? null;
        
        if (!$slug) {
            header('Location: /');
            exit;
        }
        
        // Récupère le vendeur (cherche dans shop_slug ET store_slug)
        $seller = $this->userRepo->findByShopSlug($slug);
        
        if (!$seller || $seller['role'] !== 'seller') {
            http_response_code(404);
            view('errors/404', [
                'message' => 'Cette boutique n\'existe pas.'
            ]);
            return;
        }
        
        // Track la visite (sauf si c'est le vendeur lui-même)
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $seller['id']) {
            $this->trackVisit($seller['id']);
        }
        
        // CORRECTION: Pas besoin de CAST, seller_id est bigint
        $sellerId = (int)$seller['id'];
        
        // Récupère les produits actifs SANS CAST
        $products = $this->db->fetchAll(
            "SELECT * FROM products 
             WHERE seller_id = ? 
             AND is_active = 1 
             AND (status IS NULL OR status != 'rejected')
             ORDER BY created_at DESC 
             LIMIT 50",
            [$sellerId]
        );
        
        // Log debug
        error_log("Shop $slug: seller_id=$sellerId, products found: " . count($products));
        
        // Statistiques de la boutique
        $stats = $this->getShopStats($sellerId);
        
        // Données SEO pour la page boutique
        $shopName = $seller['shop_name'] ?? $seller['store_name'] ?? $seller['name'];
        $shopDesc = $seller['shop_description'] ?? $seller['store_description'] ?? '';
        
        $seoData = [
            'title' => $shopName . ' - Boutique en Ligne',
            'description' => $shopDesc ?: 
                'Découvrez les produits numériques de ' . $shopName . ' sur Luxe Stars Power. Ebooks, formations, vidéos et plus.',
            'keywords' => $shopName . ', boutique en ligne, produits numériques, ' . $seller['name'],
            'image' => $seller['shop_banner'] ?? $seller['store_banner'] ?? ($seller['shop_logo'] ?? $seller['store_logo'] ?? 'https://luxestarspower.com/assets/images/default-shop-banner.jpg'),
            'url' => 'https://luxestarspower.com/boutique/' . $slug,
            'type' => 'profile',
            'shop' => [
                'name' => $shopName,
                'description' => $shopDesc,
                'logo' => $seller['shop_logo'] ?? $seller['store_logo'] ?? null
            ]
        ];
        
        view('front/shop/show', [
            'seller' => $seller,
            'products' => $products,
            'stats' => $stats,
            'seo' => $seoData
        ]);
    }
    
    private function trackVisit($sellerId) {
        try {
            $visitorIp = $_SERVER['REMOTE_ADDR'] ?? '';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            
            // Limite la taille des champs
            $userAgent = substr($userAgent, 0, 500);
            $referrer = substr($referrer, 0, 500);
            
            $this->db->insert(
                "INSERT INTO shop_visits (seller_id, visitor_ip, user_agent, referrer, visited_at) 
                 VALUES (?, ?, ?, ?, NOW())",
                [$sellerId, $visitorIp, $userAgent, $referrer]
            );
        } catch (\Exception $e) {
            // Silent fail - ne pas bloquer l'affichage si le tracking échoue
            error_log("Shop visit tracking failed: " . $e->getMessage());
        }
    }
    
    private function getShopStats($sellerId) {
        // CORRECTION: Cast en int, pas en string
        $sellerIdInt = (int)$sellerId;
        
        // Nombre de produits actifs SANS CAST
        $productsCount = $this->db->fetchOne(
            "SELECT COUNT(*) as count FROM products 
             WHERE seller_id = ?
             AND is_active = 1
             AND (status IS NULL OR status != 'rejected')",
            [$sellerIdInt]
        );
        
        // Nombre de ventes (commandes complétées) SANS CAST
        $salesCount = $this->db->fetchOne(
            "SELECT COUNT(DISTINCT o.id) as count 
             FROM orders o
             JOIN order_items oi ON o.id = oi.order_id
             JOIN products p ON oi.product_id = p.id
             WHERE p.seller_id = ? AND o.status = 'completed'",
            [$sellerIdInt]
        );
        
        // Nombre de visites (30 derniers jours) SANS CAST
        $visitsCount = $this->db->fetchOne(
            "SELECT COUNT(*) as count 
             FROM shop_visits 
             WHERE seller_id = ?
             AND visited_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
            [$sellerIdInt]
        );
        
        // Note moyenne des produits SANS CAST
        $avgRating = $this->db->fetchOne(
            "SELECT AVG(r.rating) as avg_rating, COUNT(r.id) as reviews_count
             FROM reviews r
             JOIN products p ON r.product_id = p.id
             WHERE p.seller_id = ?",
            [$sellerIdInt]
        );
        
        return [
            'products_count' => $productsCount['count'] ?? 0,
            'sales_count' => $salesCount['count'] ?? 0,
            'visits_count' => $visitsCount['count'] ?? 0,
            'avg_rating' => round($avgRating['avg_rating'] ?? 0, 1),
            'reviews_count' => $avgRating['reviews_count'] ?? 0
        ];
    }
}