<?php

/**
 * Migration: Create all database tables
 * Version: 001
 * Date: 2024-12-08
 */

class Migration_001_CreateAllTables
{
    private $pdo;
    
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function up()
    {
        // Users table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                role ENUM('buyer', 'seller', 'admin') NOT NULL DEFAULT 'buyer',
                bio TEXT NULL,
                avatar_url VARCHAR(255) NULL,
                currency CHAR(3) NOT NULL DEFAULT 'USD',
                settings JSON NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                email_verified_at TIMESTAMP NULL,
                two_factor_secret VARCHAR(255) NULL,
                two_factor_enabled TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_role_created (role, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Products table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS products (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                seller_id BIGINT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL UNIQUE,
                description TEXT,
                type ENUM('ebook', 'image', 'video', 'course', 'file') NOT NULL,
                price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                currency CHAR(3) NOT NULL DEFAULT 'USD',
                file_storage_path VARCHAR(511) NOT NULL,
                file_size BIGINT UNSIGNED NULL,
                file_mime_type VARCHAR(100) NULL,
                thumbnail_path VARCHAR(511) NULL,
                preview_paths JSON NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                is_featured TINYINT(1) NOT NULL DEFAULT 0,
                download_limit INT NULL,
                views_count INT UNSIGNED DEFAULT 0,
                sales_count INT UNSIGNED DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_seller_id (seller_id),
                INDEX idx_slug (slug),
                INDEX idx_type_active (type, is_active),
                INDEX idx_featured (is_featured, created_at),
                FULLTEXT idx_search (title, description)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Product versions table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS product_versions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NOT NULL,
                version_number VARCHAR(50) NOT NULL,
                storage_path VARCHAR(511) NOT NULL,
                file_size BIGINT UNSIGNED NULL,
                changelog TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                INDEX idx_product_id (product_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Categories table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL UNIQUE,
                description TEXT NULL,
                icon VARCHAR(255) NULL,
                parent_id BIGINT UNSIGNED NULL,
                display_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
                INDEX idx_slug (slug),
                INDEX idx_parent_id (parent_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Product categories pivot
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS product_categories (
                product_id BIGINT UNSIGNED NOT NULL,
                category_id BIGINT UNSIGNED NOT NULL,
                PRIMARY KEY (product_id, category_id),
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Orders table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS orders (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_number VARCHAR(64) NOT NULL UNIQUE,
                buyer_id BIGINT UNSIGNED NOT NULL,
                seller_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                seller_earnings DECIMAL(10,2) NOT NULL,
                platform_fee DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'paid', 'refunded', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
                payment_method VARCHAR(50) NULL,
                payment_reference VARCHAR(255) NULL,
                payment_data JSON NULL,
                buyer_email VARCHAR(255) NULL,
                buyer_name VARCHAR(150) NULL,
                notes TEXT NULL,
                refund_reason TEXT NULL,
                refunded_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                INDEX idx_order_number (order_number),
                INDEX idx_buyer_id (buyer_id),
                INDEX idx_seller_id (seller_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Downloads table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS downloads (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                product_id BIGINT UNSIGNED NOT NULL,
                download_token VARCHAR(255) NOT NULL UNIQUE,
                download_url TEXT NULL,
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(512) NULL,
                downloaded_at TIMESTAMP NULL,
                download_count INT UNSIGNED DEFAULT 0,
                expire_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                INDEX idx_download_token (download_token),
                INDEX idx_user_id (user_id),
                INDEX idx_expire_at (expire_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Wallets table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS wallets (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL UNIQUE,
                balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                pending_balance DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                currency CHAR(3) NOT NULL DEFAULT 'USD',
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Transactions table (ledger)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                wallet_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NULL,
                type ENUM('credit', 'debit', 'fee', 'refund', 'payout') NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                balance_before DECIMAL(10,2) NOT NULL,
                balance_after DECIMAL(10,2) NOT NULL,
                description TEXT NULL,
                metadata JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
                INDEX idx_wallet_id (wallet_id),
                INDEX idx_order_id (order_id),
                INDEX idx_type (type),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Payouts table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS payouts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                seller_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'USD',
                status ENUM('pending', 'processing', 'paid', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
                payment_method VARCHAR(50) NULL,
                account_details TEXT NULL,
                stripe_transfer_id VARCHAR(255) NULL,
                reference_number VARCHAR(100) NULL,
                notes TEXT NULL,
                processed_at TIMESTAMP NULL,
                failed_reason TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_seller_id (seller_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Webhooks logs table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS webhook_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                gateway VARCHAR(50) NOT NULL,
                event_type VARCHAR(100) NOT NULL,
                payload TEXT NOT NULL,
                signature VARCHAR(512) NULL,
                status ENUM('received', 'processing', 'processed', 'failed') NOT NULL DEFAULT 'received',
                attempts INT UNSIGNED DEFAULT 0,
                error_message TEXT NULL,
                processed_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_gateway (gateway),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Site settings table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS site_settings (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT NULL,
                setting_type VARCHAR(50) NOT NULL DEFAULT 'string',
                description TEXT NULL,
                is_public TINYINT(1) DEFAULT 0,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_setting_key (setting_key)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Activity logs table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NULL,
                action_type VARCHAR(100) NOT NULL,
                resource_type VARCHAR(100) NULL,
                resource_id BIGINT UNSIGNED NULL,
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(512) NULL,
                metadata JSON NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user_id (user_id),
                INDEX idx_action_type (action_type),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Password reset tokens
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                token VARCHAR(255) NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                used_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_token (token),
                INDEX idx_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Email verification tokens
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS email_verifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                token VARCHAR(255) NOT NULL UNIQUE,
                expires_at TIMESTAMP NOT NULL,
                verified_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_token (token),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Coupons table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS coupons (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(100) NOT NULL UNIQUE,
                type ENUM('percentage', 'fixed') NOT NULL,
                value DECIMAL(10,2) NOT NULL,
                minimum_amount DECIMAL(10,2) NULL,
                maximum_discount DECIMAL(10,2) NULL,
                usage_limit INT UNSIGNED NULL,
                usage_count INT UNSIGNED DEFAULT 0,
                starts_at TIMESTAMP NULL,
                expires_at TIMESTAMP NULL,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_code (code),
                INDEX idx_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Reviews table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS reviews (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id BIGINT UNSIGNED NOT NULL,
                user_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NOT NULL,
                rating TINYINT UNSIGNED NOT NULL,
                title VARCHAR(255) NULL,
                comment TEXT NULL,
                is_verified TINYINT(1) DEFAULT 1,
                is_approved TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                UNIQUE KEY unique_review (product_id, user_id, order_id),
                INDEX idx_product_id (product_id),
                INDEX idx_rating (rating)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Notifications table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS notifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                type VARCHAR(100) NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                data JSON NULL,
                read_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_read_at (read_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Queue jobs table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS queue_jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(100) NOT NULL DEFAULT 'default',
                payload TEXT NOT NULL,
                attempts TINYINT UNSIGNED DEFAULT 0,
                reserved_at TIMESTAMP NULL,
                available_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_queue_reserved (queue, reserved_at),
                INDEX idx_available_at (available_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Failed jobs table
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS failed_jobs (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                queue VARCHAR(100) NOT NULL,
                payload TEXT NOT NULL,
                exception TEXT NOT NULL,
                failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Insert default site settings
        $this->insertDefaultSettings();
        
        return true;
    }
    
    public function down()
    {
        $tables = [
            'failed_jobs', 'queue_jobs', 'notifications', 'reviews', 'coupons',
            'email_verifications', 'password_resets', 'activity_logs', 'site_settings',
            'webhook_logs', 'payouts', 'transactions', 'wallets', 'downloads',
            'orders', 'product_categories', 'categories', 'product_versions',
            'products', 'users'
        ];
        
        foreach ($tables as $table) {
            $this->pdo->exec("DROP TABLE IF EXISTS $table");
        }
        
        return true;
    }
    
    private function insertDefaultSettings()
    {
        $settings = [
            ['platform_fee_percentage', '20.00', 'decimal', 'Platform commission percentage', 1],
            ['platform_fee_fixed', '0.00', 'decimal', 'Fixed platform fee', 1],
            ['minimum_payout_amount', '50.00', 'decimal', 'Minimum amount for payout', 1],
            ['default_currency', 'USD', 'string', 'Default currency', 1],
            ['site_name', 'LuxeStarsPower', 'string', 'Site name', 1],
            ['max_upload_size', '2147483648', 'integer', 'Maximum file upload size in bytes', 1],
            ['download_link_expiry', '3600', 'integer', 'Download link expiry in seconds', 1],
            ['max_download_attempts', '5', 'integer', 'Maximum download attempts', 1],
            ['maintenance_mode', '0', 'boolean', 'Maintenance mode', 0],
            ['registration_enabled', '1', 'boolean', 'Allow new registrations', 1],
            ['seller_approval_required', '0', 'boolean', 'Require approval for sellers', 1],
            ['product_approval_required', '0', 'boolean', 'Require approval for products', 1],
        ];
        
        $stmt = $this->pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value, setting_type, description, is_public)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($settings as $setting) {
            $stmt->execute($setting);
        }
    }
}
