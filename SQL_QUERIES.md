# SQL Queries Utiles - Luxe Stars Power

## Administration rapide

### Créer admin manuellement (si script échoue)
```sql
INSERT INTO users (name, email, password_hash, role, is_active, email_verified_at, created_at) 
VALUES (
    'Admin',
    'admin@luxestarspower.com',
    '$argon2id$v=19$m=65536,t=4,p=1$...',  -- Utiliser password_hash() en PHP
    'admin',
    1,
    NOW(),
    NOW()
);
```

### Promouvoir utilisateur en admin
```sql
UPDATE users SET role = 'admin' WHERE email = 'user@domain.com';
```

### Promouvoir en vendeur
```sql
UPDATE users SET role = 'seller' WHERE email = 'user@domain.com';
```

### Activer/Désactiver utilisateur
```sql
UPDATE users SET is_active = 1 WHERE id = 123;  -- Activer
UPDATE users SET is_active = 0 WHERE id = 123;  -- Désactiver
```

## Produits

### Mettre en vedette
```sql
UPDATE products SET is_featured = 1 WHERE id = 456;
```

### Activer/Désactiver produit
```sql
UPDATE products SET is_active = 0 WHERE id = 456;  -- Désactiver
```

### Top produits par ventes
```sql
SELECT id, title, sales, price, (sales * price) as revenue 
FROM products 
ORDER BY sales DESC 
LIMIT 10;
```

### Produits sans ventes
```sql
SELECT id, title, created_at 
FROM products 
WHERE sales = 0 
ORDER BY created_at DESC;
```

## Commandes & Revenus

### Revenue total plateforme
```sql
SELECT SUM(platform_fee) as total_commission 
FROM orders 
WHERE status = 'paid';
```

### Revenue par vendeur
```sql
SELECT 
    u.name as seller_name,
    u.email,
    COUNT(o.id) as total_sales,
    SUM(o.seller_earnings) as total_earnings
FROM orders o
JOIN users u ON o.seller_id = u.id
WHERE o.status = 'paid'
GROUP BY o.seller_id
ORDER BY total_earnings DESC;
```

### Commandes récentes
```sql
SELECT 
    o.order_number,
    o.amount,
    o.status,
    p.title as product,
    u.name as buyer,
    o.created_at
FROM orders o
JOIN products p ON o.product_id = p.id
JOIN users u ON o.buyer_id = u.id
ORDER BY o.created_at DESC
LIMIT 20;
```

### Remboursements
```sql
UPDATE orders SET status = 'refunded' WHERE id = 789;

-- Créer transaction de remboursement
INSERT INTO transactions (user_id, order_id, type, amount, balance_after, created_at)
VALUES (
    (SELECT seller_id FROM orders WHERE id = 789),
    789,
    'refund',
    (SELECT -seller_earnings FROM orders WHERE id = 789),
    0,
    NOW()
);
```

## Analytics

### Statistiques globales
```sql
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'buyer') as total_buyers,
    (SELECT COUNT(*) FROM users WHERE role = 'seller') as total_sellers,
    (SELECT COUNT(*) FROM products WHERE is_active = 1) as active_products,
    (SELECT COUNT(*) FROM orders WHERE status = 'paid') as total_orders,
    (SELECT SUM(amount) FROM orders WHERE status = 'paid') as total_revenue,
    (SELECT SUM(platform_fee) FROM orders WHERE status = 'paid') as total_commission;
```

### Ventes par jour (derniers 30 jours)
```sql
SELECT 
    DATE(created_at) as date,
    COUNT(*) as orders,
    SUM(amount) as revenue
FROM orders
WHERE status = 'paid' 
AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(created_at)
ORDER BY date DESC;
```

### Top vendeurs du mois
```sql
SELECT 
    u.name,
    u.email,
    COUNT(o.id) as sales,
    SUM(o.seller_earnings) as earnings
FROM orders o
JOIN users u ON o.seller_id = u.id
WHERE o.status = 'paid'
AND MONTH(o.created_at) = MONTH(NOW())
AND YEAR(o.created_at) = YEAR(NOW())
GROUP BY o.seller_id
ORDER BY earnings DESC
LIMIT 10;
```

## Payouts

### Payouts en attente
```sql
SELECT 
    p.id,
    u.name as seller,
    u.email,
    p.amount,
    p.created_at
FROM payouts p
JOIN users u ON p.seller_id = u.id
WHERE p.status = 'pending'
ORDER BY p.created_at ASC;
```

### Marquer payout comme payé
```sql
UPDATE payouts 
SET status = 'paid', processed_at = NOW() 
WHERE id = 123;
```

### Solde vendeur (non payé)
```sql
SELECT 
    u.id,
    u.name,
    u.email,
    SUM(o.seller_earnings) as unpaid_balance
FROM users u
JOIN orders o ON u.id = o.seller_id
WHERE o.status = 'paid'
AND o.id NOT IN (
    SELECT order_id FROM transactions 
    WHERE type = 'payout' AND order_id IS NOT NULL
)
GROUP BY u.id
HAVING unpaid_balance >= 50.00  -- Seuil minimum
ORDER BY unpaid_balance DESC;
```

## Téléchargements

### Nettoyer tokens expirés
```sql
DELETE FROM downloads WHERE expire_at < NOW();
```

### Stats téléchargements
```sql
SELECT 
    p.title,
    COUNT(d.id) as total_downloads,
    COUNT(DISTINCT d.user_id) as unique_users
FROM downloads d
JOIN products p ON d.product_id = p.id
WHERE d.downloaded_at IS NOT NULL
GROUP BY d.product_id
ORDER BY total_downloads DESC;
```

## Sécurité & Logs

### Activité récente
```sql
SELECT 
    al.action_type,
    u.name,
    u.email,
    al.ip_address,
    al.created_at
FROM activity_logs al
LEFT JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 50;
```

### Tentatives login échouées
```sql
SELECT 
    ip_address,
    COUNT(*) as attempts,
    MAX(created_at) as last_attempt
FROM activity_logs
WHERE action_type = 'failed_login'
AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
GROUP BY ip_address
HAVING attempts >= 5
ORDER BY attempts DESC;
```

### Webhooks non traités
```sql
SELECT * FROM webhooks_logs 
WHERE status = 'pending' 
OR (status = 'failed' AND attempts < 3)
ORDER BY created_at ASC;
```

## Maintenance

### Nettoyer vieux logs (>90 jours)
```sql
DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
DELETE FROM webhooks_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Optimiser tables
```sql
OPTIMIZE TABLE users, products, orders, downloads, payouts, transactions;
```

### Analyser performance requêtes
```sql
SHOW FULL PROCESSLIST;
EXPLAIN SELECT * FROM products WHERE is_active = 1 AND is_featured = 1;
```

## Settings

### Changer commission
```sql
UPDATE site_settings SET setting_value = '0.15' WHERE setting_key = 'commission_rate';  -- 15%
```

### Changer seuil payout
```sql
UPDATE site_settings SET setting_value = '100.00' WHERE setting_key = 'payout_threshold';
```

### Mode maintenance
```sql
UPDATE site_settings SET setting_value = '1' WHERE setting_key = 'maintenance_mode';  -- ON
UPDATE site_settings SET setting_value = '0' WHERE setting_key = 'maintenance_mode';  -- OFF
```

## Backup & Restore

### Export DB
```bash
mysqldump -u root -p luxestarspower > backup_$(date +%Y%m%d).sql
mysqldump -u root -p luxestarspower | gzip > backup_$(date +%Y%m%d).sql.gz
```

### Import DB
```bash
mysql -u root -p luxestarspower < backup.sql
gunzip < backup.sql.gz | mysql -u root -p luxestarspower
```

### Backup table spécifique
```bash
mysqldump -u root -p luxestarspower users products orders > backup_core.sql
```

## Debug

### Vérifier intégrité relations
```sql
-- Commandes sans produit valide
SELECT o.* FROM orders o 
LEFT JOIN products p ON o.product_id = p.id 
WHERE p.id IS NULL;

-- Produits sans vendeur valide
SELECT p.* FROM products p 
LEFT JOIN users u ON p.seller_id = u.id 
WHERE u.id IS NULL;
```

### Taille tables
```sql
SELECT 
    table_name,
    ROUND((data_length + index_length) / 1024 / 1024, 2) AS size_mb
FROM information_schema.tables
WHERE table_schema = 'luxestarspower'
ORDER BY size_mb DESC;
```

---

**Note**: Toujours faire un backup avant modifications importantes en production !
