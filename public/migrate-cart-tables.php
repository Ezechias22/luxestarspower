<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "<h1>Migration des tables Panier & Commandes</h1>";
    echo "<pre>";
    
    // 1. Table cart
    echo "🔄 Création de la table 'cart'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_product (user_id, product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'cart' créée avec succès !\n\n";
    
    // 2. Table orders
    echo "🔄 Création de la table 'orders'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
            payment_method VARCHAR(50),
            payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_payment_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'orders' créée avec succès !\n\n";
    
    // 3. Table order_items
    echo "🔄 Création de la table 'order_items'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            seller_id INT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            quantity INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_order_id (order_id),
            INDEX idx_seller_id (seller_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'order_items' créée avec succès !\n\n";
    
    // Vérification
    echo "📋 Vérification des tables créées :\n";
    $tables = $db->fetchAll("SHOW TABLES LIKE 'cart'");
    echo "   - cart: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    $tables = $db->fetchAll("SHOW TABLES LIKE 'orders'");
    echo "   - orders: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    $tables = $db->fetchAll("SHOW TABLES LIKE 'order_items'");
    echo "   - order_items: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    echo "\n✅ Migration terminée avec succès !\n";
    echo "🗑️  N'oubliez pas de supprimer ce fichier après : public/migrate-cart-tables.php\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>❌ ERREUR</h1>";
    echo "<pre>";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>