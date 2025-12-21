<?php
namespace App\Controllers;

use App\Database;

class SitemapController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function generate($params = []) {  // ← AJOUTE $params = []
        header('Content-Type: application/xml; charset=utf-8');
        
        $baseUrl = 'https://luxestarspower.com';
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    
    <!-- Pages principales -->
    <url>
        <loc><?php echo $baseUrl; ?>/</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/produits</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc><?php echo $baseUrl; ?>/vendre</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <?php
    try {
        // Produits
        $products = $this->db->fetchAll("
            SELECT slug, updated_at 
            FROM products 
            WHERE is_active = 1 
            ORDER BY created_at DESC
            LIMIT 100
        ");
        
        foreach ($products as $product):
        ?>
        <url>
            <loc><?php echo $baseUrl; ?>/produit/<?php echo htmlspecialchars($product['slug']); ?></loc>
            <lastmod><?php echo date('Y-m-d', strtotime($product['updated_at'])); ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
        <?php
        endforeach;
        
        // Boutiques vendeurs
        $shops = $this->db->fetchAll("
            SELECT shop_slug, updated_at 
            FROM users 
            WHERE role = 'seller' 
            AND shop_slug IS NOT NULL
        ");
        
        foreach ($shops as $shop):
        ?>
        <url>
            <loc><?php echo $baseUrl; ?>/boutique/<?php echo htmlspecialchars($shop['shop_slug']); ?></loc>
            <lastmod><?php echo date('Y-m-d', strtotime($shop['updated_at'])); ?></lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.6</priority>
        </url>
        <?php
        endforeach;
    } catch (\Exception $e) {
        error_log("Sitemap generation error: " . $e->getMessage());
    }
    ?>
</urlset>
<?php
        exit;
    }
}