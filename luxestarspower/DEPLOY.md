# Déploiement Luxe Stars Power

## Installation rapide

1. **Base de données**
```bash
mysql -u root -p
CREATE DATABASE luxestarspower CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
php migrations/001_initial_schema.php
```

2. **Configuration**
```bash
cp .env.example .env
# Éditer .env avec vos credentials
composer install --no-dev
```

3. **Nginx**
```bash
cp docker/nginx.conf /etc/nginx/sites-available/luxestarspower
ln -s /etc/nginx/sites-available/luxestarspower /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx
```

4. **SSL**
```bash
certbot --nginx -d luxestarspower.com
```

5. **Admin**
```bash
php scripts/create_admin.php admin@domain.com "Admin Name"
```

## Webhooks
- Stripe: https://luxestarspower.com/webhooks/stripe
- PayPal: https://luxestarspower.com/webhooks/paypal

## Backups
```bash
# Cron quotidien 2h
0 2 * * * mysqldump luxestarspower | gzip > /backups/db_$(date +\%Y\%m\%d).sql.gz
```

## Monitoring
- Logs: storage/logs/
- Métriques: admin dashboard
