<?php
/**
 * Sitemap Produits & Boutiques - Luxe Stars Power
 */

header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    // Récupère tous les produits actifs
    $products = $db->fetchAll(
        "SELECT slug, updated_at 
         FROM products 
         WHERE is_active = 1 
         AND (status IS NULL OR status != 'rejected')
         ORDER BY updated_at DESC"
    );
    
    // Récupère toutes les boutiques
    $shops = $db->fetchAll(
        "SELECT shop_slug, updated_at 
         FROM users 
         WHERE role = 'seller' 
         AND shop_slug IS NOT NULL 
         AND shop_slug != ''"
    );
    
} catch (Exception $e) {
    $products = [];
    $shops = [];
}

// Génère le XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

// ========== PRODUITS ==========
foreach ($products as $product) {
    if (empty($product['slug'])) continue;
    
    $lastmod = $product['updated_at'] 
        ? date('Y-m-d', strtotime($product['updated_at'])) 
        : date('Y-m-d');
        
    echo '  <url>' . PHP_EOL;
    echo '    <loc>https://luxestarspower.com/produit/' . htmlspecialchars($product['slug'], ENT_XML1) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.8</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

// ========== BOUTIQUES ==========
foreach ($shops as $shop) {
    if (empty($shop['shop_slug'])) continue;
    
    $lastmod = $shop['updated_at'] 
        ? date('Y-m-d', strtotime($shop['updated_at'])) 
        : date('Y-m-d');
        
    echo '  <url>' . PHP_EOL;
    echo '    <loc>https://luxestarspower.com/boutique/' . htmlspecialchars($shop['shop_slug'], ENT_XML1) . '</loc>' . PHP_EOL;
    echo '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
    echo '    <changefreq>weekly</changefreq>' . PHP_EOL;
    echo '    <priority>0.7</priority>' . PHP_EOL;
    echo '  </url>' . PHP_EOL;
}

echo '</urlset>';