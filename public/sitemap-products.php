<?php
header('Content-Type: application/xml; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;

$db = Database::getInstance();
$products = $db->fetchAll("SELECT slug, updated_at FROM products WHERE is_active = 1 AND (status IS NULL OR status != 'rejected')");
$shops = $db->fetchAll("SELECT shop_slug, updated_at FROM users WHERE role = 'seller' AND shop_slug IS NOT NULL AND shop_slug != ''");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($products as $p): ?>
  <url>
    <loc>https://luxestarspower.com/produit/<?= htmlspecialchars($p['slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($p['updated_at'] ?? 'now')) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
  </url>
<?php endforeach; ?>
<?php foreach ($shops as $s): ?>
  <url>
    <loc>https://luxestarspower.com/boutique/<?= htmlspecialchars($s['shop_slug']) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($s['updated_at'] ?? 'now')) ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.7</priority>
  </url>
<?php endforeach; ?>
</urlset>