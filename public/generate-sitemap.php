<?php
/**
 * Générateur de Sitemap Dynamique
 * Luxe Stars Power - Marketplace Premium
 * 
 * Usage: Accéder à https://luxestarspower.com/generate-sitemap.php
 * ou exécuter via CLI: php generate-sitemap.php
 */

// Charge l'autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Démarre la session
session_start();

use App\Database;

try {
    $db = Database::getInstance();
    
    // Récupère tous les produits actifs
    $products = $db->fetchAll(
        "SELECT slug, updated_at 
         FROM products 
         WHERE is_active = 1 
         AND (status IS NULL OR status != 'rejected')
         ORDER BY updated_at DESC 
         LIMIT 1000"
    );
    
    // Récupère toutes les boutiques actives
    $shops = $db->fetchAll(
        "SELECT shop_slug, updated_at 
         FROM users 
         WHERE role = 'seller' 
         AND shop_slug IS NOT NULL 
         AND shop_slug != ''
         ORDER BY updated_at DESC 
         LIMIT 500"
    );
    
    // Commence le XML
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
    $xml .= ' xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . PHP_EOL;
    
    // ========== PAGES STATIQUES ==========
    $staticPages = [
        // Pages principales
        ['loc' => '', 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => 'produits', 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => 'vendre', 'priority' => '0.8', 'changefreq' => 'monthly'],
        
        // Pages d'information
        ['loc' => 'contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ['loc' => 'faq', 'priority' => '0.6', 'changefreq' => 'monthly'],
        
        // Pages légales
        ['loc' => 'conditions', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['loc' => 'confidentialite', 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['loc' => 'politique-remboursement', 'priority' => '0.4', 'changefreq' => 'monthly'],
        
        // Authentification
        ['loc' => 'connexion', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ['loc' => 'inscription', 'priority' => '0.3', 'changefreq' => 'yearly'],
    ];
    
    foreach ($staticPages as $page) {
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>https://luxestarspower.com/' . $page['loc'] . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . $page['changefreq'] . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $page['priority'] . '</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
    
    // ========== PRODUITS ==========
    foreach ($products as $product) {
        $lastmod = $product['updated_at'] 
            ? date('Y-m-d', strtotime($product['updated_at'])) 
            : date('Y-m-d');
            
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>https://luxestarspower.com/produit/' . htmlspecialchars($product['slug']) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.7</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
    
    // ========== BOUTIQUES ==========
    foreach ($shops as $shop) {
        $lastmod = $shop['updated_at'] 
            ? date('Y-m-d', strtotime($shop['updated_at'])) 
            : date('Y-m-d');
            
        $xml .= '  <url>' . PHP_EOL;
        $xml .= '    <loc>https://luxestarspower.com/boutique/' . htmlspecialchars($shop['shop_slug']) . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
        $xml .= '    <priority>0.6</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
    }
    
    // Ferme le XML
    $xml .= '</urlset>';
    
    // Sauvegarde dans le fichier
    $result = file_put_contents(__DIR__ . '/sitemap.xml', $xml);
    
    if ($result === false) {
        throw new Exception("Erreur lors de l'écriture du fichier sitemap.xml");
    }
    
    // Statistiques
    $totalUrls = count($staticPages) + count($products) + count($shops);
    
    // Affichage du résultat
    if (php_sapi_name() === 'cli') {
        // Mode CLI
        echo "✅ Sitemap généré avec succès !\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "📄 Pages statiques: " . count($staticPages) . "\n";
        echo "📦 Produits: " . count($products) . "\n";
        echo "🏪 Boutiques: " . count($shops) . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🌐 Total URLs: $totalUrls\n";
        echo "📍 Fichier: " . __DIR__ . "/sitemap.xml\n";
        echo "🔗 URL: https://luxestarspower.com/sitemap.xml\n";
    } else {
        // Mode Web
        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html>";
        echo "<html lang='fr'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
        echo "<title>Sitemap Generator - Luxe Stars Power</title>";
        echo "<style>";
        echo "body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin: 0; padding: 40px; }";
        echo ".container { max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }";
        echo "h1 { color: #667eea; margin: 0 0 30px; text-align: center; }";
        echo ".success { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745; }";
        echo ".stats { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }";
        echo ".stat-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #dee2e6; }";
        echo ".stat-item:last-child { border-bottom: none; }";
        echo ".stat-label { font-weight: 600; color: #495057; }";
        echo ".stat-value { color: #667eea; font-weight: bold; }";
        echo ".link { background: #667eea; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-block; margin-top: 20px; text-align: center; }";
        echo ".link:hover { background: #5568d3; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<div class='container'>";
        echo "<h1>✅ Sitemap Généré !</h1>";
        echo "<div class='success'>Le sitemap a été généré avec succès et sauvegardé.</div>";
        echo "<div class='stats'>";
        echo "<div class='stat-item'><span class='stat-label'>📄 Pages statiques</span><span class='stat-value'>" . count($staticPages) . "</span></div>";
        echo "<div class='stat-item'><span class='stat-label'>📦 Produits</span><span class='stat-value'>" . count($products) . "</span></div>";
        echo "<div class='stat-item'><span class='stat-label'>🏪 Boutiques</span><span class='stat-value'>" . count($shops) . "</span></div>";
        echo "<div class='stat-item'><span class='stat-label'>🌐 Total URLs</span><span class='stat-value'>$totalUrls</span></div>";
        echo "</div>";
        echo "<div style='text-align: center;'>";
        echo "<a href='/sitemap.xml' target='_blank' class='link'>🔗 Voir le sitemap.xml</a>";
        echo "<br><br>";
        echo "<a href='/' class='link'>🏠 Retour à l'accueil</a>";
        echo "</div>";
        echo "</div>";
        echo "</body>";
        echo "</html>";
    }
    
} catch (Exception $e) {
    if (php_sapi_name() === 'cli') {
        echo "❌ ERREUR: " . $e->getMessage() . "\n";
    } else {
        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html>";
        echo "<html><head><meta charset='UTF-8'><title>Erreur</title></head><body>";
        echo "<h1 style='color: red;'>❌ Erreur</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "</body></html>";
    }
    exit(1);
}