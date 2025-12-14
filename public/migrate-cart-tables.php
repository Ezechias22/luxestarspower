<?php
require __DIR__ . '/../app/Database.php';

use App\Database;

try {
    $db = Database::getInstance();
    
    echo "<h1>Migration des tables Panier & Commandes</h1>";
    echo "<pre>";
    
    // Vérification du type de users.id
    echo "🔍 Vérification du type de users.id...\n";
    $userIdType = $db->fetchOne("SHOW COLUMNS FROM users WHERE Field = 'id'");
    echo "   Type actuel: " . $userIdType['Type'] . "\n\n";
    
    // Détermine le type d'ID à utiliser
    $idType = (stripos($userIdType['Type'], 'bigint') !== false) ? 'BIGINT UNSIGNED' : 'INT UNSIGNED';
    
    echo "   Type à utiliser pour les foreign keys: $idType\n\n";
    
    // 1. Table cart
    echo "🔄 Création de la table 'cart'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS cart (
            id $idType AUTO_INCREMENT PRIMARY KEY,
            user_id $idType NOT NULL,
            product_id $idType NOT NULL,
            quantity INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_user_product (user_id, product_id),
            INDEX idx_user_id (user_id),
            INDEX idx_product_id (product_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'cart' créée avec succès !\n\n";
    
    // Ajoute les foreign keys après création
    echo "🔄 Ajout des contraintes de clé étrangère pour 'cart'...\n";
    try {
        $db->query("
            ALTER TABLE cart 
            ADD CONSTRAINT fk_cart_user 
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_cart_user ajoutée\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte fk_cart_user existe déjà\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $db->query("
            ALTER TABLE cart 
            ADD CONSTRAINT fk_cart_product 
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_cart_product ajoutée\n\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte fk_cart_product existe déjà\n\n";
        } else {
            throw $e;
        }
    }
    
    // 2. Table orders
    echo "🔄 Création de la table 'orders'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS orders (
            id $idType AUTO_INCREMENT PRIMARY KEY,
            user_id $idType NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
            payment_method VARCHAR(50),
            payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_user_id (user_id),
            INDEX idx_status (status),
            INDEX idx_payment_status (payment_status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'orders' créée avec succès !\n\n";
    
    echo "🔄 Ajout des contraintes de clé étrangère pour 'orders'...\n";
    try {
        $db->query("
            ALTER TABLE orders 
            ADD CONSTRAINT fk_orders_user 
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_orders_user ajoutée\n\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte fk_orders_user existe déjà\n\n";
        } else {
            throw $e;
        }
    }
    
    // 3. Table order_items
    echo "🔄 Création de la table 'order_items'...\n";
    $db->query("
        CREATE TABLE IF NOT EXISTS order_items (
            id $idType AUTO_INCREMENT PRIMARY KEY,
            order_id $idType NOT NULL,
            product_id $idType NOT NULL,
            seller_id $idType NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            quantity INT DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_order_id (order_id),
            INDEX idx_product_id (product_id),
            INDEX idx_seller_id (seller_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Table 'order_items' créée avec succès !\n\n";
    
    echo "🔄 Ajout des contraintes de clé étrangère pour 'order_items'...\n";
    try {
        $db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_order 
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_order_items_order ajoutée\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte existe déjà\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_product 
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_order_items_product ajoutée\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte existe déjà\n";
        } else {
            throw $e;
        }
    }
    
    try {
        $db->query("
            ALTER TABLE order_items 
            ADD CONSTRAINT fk_order_items_seller 
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "✅ Contrainte fk_order_items_seller ajoutée\n\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "ℹ️  Contrainte existe déjà\n\n";
        } else {
            throw $e;
        }
    }
    
    // Vérification finale
    echo "📋 Vérification des tables créées :\n";
    $tables = $db->fetchAll("SHOW TABLES LIKE 'cart'");
    echo "   - cart: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    $tables = $db->fetchAll("SHOW TABLES LIKE 'orders'");
    echo "   - orders: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    $tables = $db->fetchAll("SHOW TABLES LIKE 'order_items'");
    echo "   - order_items: " . (count($tables) > 0 ? "✓" : "✗") . "\n";
    
    echo "\n✅ Migration terminée avec succès !\n";
    echo "🗑️  N'oubliez pas de supprimer ce fichier : public/migrate-cart-tables.php\n";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h1>❌ ERREUR</h1>";
    echo "<pre>";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "Fichier: " . $e->getFile() . "\n";
    echo "Ligne: " . $e->getLine() . "\n\n";
    echo "Stack trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>