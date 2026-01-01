<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

header('Content-Type: text/html; charset=utf-8');
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5;line-height:1.6;}</style>";
echo "<h1>🚀 Migration des abonnements - Luxe Stars Power</h1>";
echo "<hr>";

try {
    $db = Database::getInstance();
    $success = 0;
    $skipped = 0;
    
    // ===== TABLE SUBSCRIPTION_PLANS =====
    echo "<p>📋 Création de la table <strong>subscription_plans</strong>...</p>";
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS subscription_plans (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                slug VARCHAR(50) UNIQUE NOT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                currency VARCHAR(3) DEFAULT 'USD',
                billing_period ENUM('trial', 'monthly', 'yearly') NOT NULL,
                trial_days INT DEFAULT 0,
                features JSON,
                max_products INT DEFAULT 0,
                commission_rate DECIMAL(5,2) DEFAULT 15.00,
                is_active BOOLEAN DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_slug (slug),
                INDEX idx_is_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Table subscription_plans créée avec succès<br>";
        $success++;
    } catch (Exception $e) {
        echo "⚠️ " . $e->getMessage() . "<br>";
    }
    
    // ===== TABLE USER_SUBSCRIPTIONS =====
    echo "<p>📋 Création de la table <strong>user_subscriptions</strong>...</p>";
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS user_subscriptions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                plan_id BIGINT UNSIGNED NOT NULL,
                status ENUM('trial', 'active', 'cancelled', 'expired', 'past_due') DEFAULT 'trial',
                trial_ends_at TIMESTAMP NULL,
                current_period_start TIMESTAMP NULL,
                current_period_end TIMESTAMP NULL,
                cancel_at_period_end BOOLEAN DEFAULT 0,
                cancelled_at TIMESTAMP NULL,
                stripe_subscription_id VARCHAR(255) NULL,
                stripe_customer_id VARCHAR(255) NULL,
                metadata JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE RESTRICT,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_current_period_end (current_period_end)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Table user_subscriptions créée avec succès<br>";
        $success++;
    } catch (Exception $e) {
        echo "⚠️ " . $e->getMessage() . "<br>";
    }
    
    // ===== TABLE SUBSCRIPTION_PAYMENTS =====
    echo "<p>📋 Création de la table <strong>subscription_payments</strong>...</p>";
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS subscription_payments (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                subscription_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                currency VARCHAR(3) DEFAULT 'USD',
                status ENUM('pending', 'succeeded', 'failed', 'refunded') DEFAULT 'pending',
                stripe_payment_intent_id VARCHAR(255) NULL,
                stripe_invoice_id VARCHAR(255) NULL,
                failure_reason TEXT NULL,
                paid_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE,
                INDEX idx_subscription_id (subscription_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Table subscription_payments créée avec succès<br>";
        $success++;
    } catch (Exception $e) {
        echo "⚠️ " . $e->getMessage() . "<br>";
    }
    
    // ===== TABLE SUBSCRIPTION_FEATURES_USAGE =====
    echo "<p>📋 Création de la table <strong>subscription_features_usage</strong>...</p>";
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS subscription_features_usage (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                subscription_id BIGINT UNSIGNED NOT NULL,
                feature_type ENUM('featured_product', 'priority_support', 'marketing_training') NOT NULL,
                feature_data JSON NULL,
                used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE,
                INDEX idx_subscription_id (subscription_id),
                INDEX idx_feature_type (feature_type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✅ Table subscription_features_usage créée avec succès<br>";
        $success++;
    } catch (Exception $e) {
        echo "⚠️ " . $e->getMessage() . "<br>";
    }
    
    // ===== INSERTION DES PLANS =====
    echo "<hr><h2>💎 Insertion des plans d'abonnement</h2>";
    
    // Vérifie si les plans existent déjà
    $existingPlans = $db->fetchOne("SELECT COUNT(*) as count FROM subscription_plans");
    
    if ($existingPlans['count'] == 0) {
        // Plan Essai Gratuit
        try {
            $db->insert("
                INSERT INTO subscription_plans (name, slug, price, billing_period, trial_days, max_products, commission_rate, features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    'Essai Gratuit',
                    'trial',
                    0.00,
                    'trial',
                    14,
                    3,
                    15.00,
                    json_encode(['Boutique personnalisée', '3 produits maximum', 'Commission 15%', 'Support email'])
                ]
            );
            echo "<p>✅ Plan <strong>'Essai Gratuit'</strong> créé - 14 jours, 3 produits max, 15% commission</p>";
            $success++;
        } catch (Exception $e) {
            echo "<p>❌ Erreur plan Essai: " . $e->getMessage() . "</p>";
        }
        
        // Plan Mensuel
        try {
            $db->insert("
                INSERT INTO subscription_plans (name, slug, price, billing_period, trial_days, max_products, commission_rate, features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    'Plan Mensuel',
                    'monthly',
                    19.99,
                    'monthly',
                    0,
                    -1,
                    10.00,
                    json_encode(['Produits illimités', 'Badge Vendeur Premium', 'Mise en avant', 'Statistiques avancées', 'Commission 10%', 'Support prioritaire'])
                ]
            );
            echo "<p>✅ Plan <strong>'Mensuel'</strong> créé - \$19.99/mois, produits illimités, 10% commission</p>";
            $success++;
        } catch (Exception $e) {
            echo "<p>❌ Erreur plan Mensuel: " . $e->getMessage() . "</p>";
        }
        
        // Plan Annuel
        try {
            $db->insert("
                INSERT INTO subscription_plans (name, slug, price, billing_period, trial_days, max_products, commission_rate, features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    'Plan Annuel',
                    'yearly',
                    199.00,
                    'yearly',
                    0,
                    -1,
                    5.00,
                    json_encode(['Tout du plan mensuel', 'Badge Vendeur Elite', 'Commission 5%', 'Produit en vedette 1x/mois', 'Formation marketing', 'Support VIP 24/7'])
                ]
            );
            echo "<p>✅ Plan <strong>'Annuel'</strong> créé - \$199/an, produits illimités, 5% commission</p>";
            $success++;
        } catch (Exception $e) {
            echo "<p>❌ Erreur plan Annuel: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>ℹ️ <strong>{$existingPlans['count']} plans</strong> déjà existants (pas de duplication)</p>";
        $skipped++;
    }
    
    // ===== MODIFICATION TABLE USERS =====
    echo "<hr><h2>👤 Modification de la table users</h2>";
    try {
        $db->query("ALTER TABLE users ADD COLUMN current_subscription_id BIGINT UNSIGNED NULL AFTER role");
        echo "<p>✅ Colonne <strong>current_subscription_id</strong> ajoutée à users</p>";
        $success++;
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "<p>ℹ️ Colonne current_subscription_id existe déjà</p>";
            $skipped++;
        } else {
            echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
        }
    }
    
    // ===== MODIFICATION TABLE PRODUCTS =====
    echo "<hr><h2>📦 Modification de la table products</h2>";
    try {
        $db->query("ALTER TABLE products ADD COLUMN is_featured BOOLEAN DEFAULT 0 AFTER is_on_sale");
        echo "<p>✅ Colonne <strong>is_featured</strong> ajoutée à products</p>";
        $success++;
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "<p>ℹ️ Colonne is_featured existe déjà</p>";
            $skipped++;
        } else {
            echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
        }
    }
    
    try {
        $db->query("ALTER TABLE products ADD COLUMN featured_until TIMESTAMP NULL AFTER is_featured");
        echo "<p>✅ Colonne <strong>featured_until</strong> ajoutée à products</p>";
        $success++;
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "<p>ℹ️ Colonne featured_until existe déjà</p>";
            $skipped++;
        } else {
            echo "<p>❌ Erreur: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h2>🎉 MIGRATION TERMINÉE !</h2>";
    echo "<p>✅ Opérations réussies: <strong>$success</strong></p>";
    echo "<p>⏭️ Opérations ignorées: <strong>$skipped</strong></p>";
    echo "<hr>";
    echo "<p><a href='/check-migration.php' style='background:#667eea;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;display:inline-block;'>➡️ Vérifier la migration</a></p>";
    
} catch (Exception $e) {
    echo "<hr>";
    echo "<h2>❌ ERREUR CRITIQUE</h2>";
    echo "<pre style='background:#fee;padding:20px;border-radius:8px;'>" . $e->getMessage() . "</pre>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre style='background:#f5f5f5;padding:20px;border-radius:8px;font-size:0.9em;'>" . $e->getTraceAsString() . "</pre>";
}
?>