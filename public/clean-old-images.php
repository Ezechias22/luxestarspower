<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "<h1>Nettoyage des anciennes images</h1>";
    echo "<pre>";
    
    // Trouve les produits avec des chemins d'images locaux
    $products = $db->fetchAll("SELECT id, title, thumbnail_path FROM products WHERE thumbnail_path LIKE '/uploads/%'");
    
    echo "🔍 Trouvé " . count($products) . " produits avec images locales\n\n";
    
    foreach ($products as $product) {
        echo "📦 Produit: {$product['title']}\n";
        echo "   Image locale: {$product['thumbnail_path']}\n";
        
        // Supprime le chemin de l'image (garde le produit mais sans image)
        $db->query("UPDATE products SET thumbnail_path = NULL WHERE id = ?", [$product['id']]);
        
        echo "   ✅ Image supprimée de la BDD\n\n";
    }
    
    echo "✅ Nettoyage terminé !\n";
    echo "ℹ️  Les produits existent toujours mais sans images.\n";
    echo "ℹ️  Re-uploadez les images depuis le panel vendeur.\n";
    echo "🗑️  Supprimez ce fichier après : public/clean-old-images.php\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>❌ ERREUR</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>