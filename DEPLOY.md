# Guide de Déploiement - LuxeStarsPower

Ce guide détaille le processus complet de déploiement en production de la plateforme LuxeStarsPower.

## Prérequis Serveur

### Serveur recommandé (Production)
- **CPU**: 4 cores minimum (8 cores recommandé)
- **RAM**: 8GB minimum (16GB recommandé)
- **Stockage**: 100GB SSD minimum
- **OS**: Ubuntu 22.04 LTS ou Debian 11
- **Bande passante**: 1Gbps

### Stack Logiciel
- PHP 8.1 ou supérieur
- Nginx 1.18+
- MySQL 8.0+
- Redis 6+ (optionnel mais recommandé)
- Supervisor (pour queue workers)
- Certbot (pour SSL gratuit)

## Étape 1: Préparation du Serveur

### 1.1 Mise à jour du système

```bash
sudo apt update && sudo apt upgrade -y
```

### 1.2 Installation de PHP 8.1

```bash
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.1-fpm php8.1-cli php8.1-common php8.1-mysql \
    php8.1-mbstring php8.1-xml php8.1-curl php8.1-gd php8.1-zip \
    php8.1-bcmath php8.1-intl php8.1-redis php8.1-imagick
```

### 1.3 Installation de Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 1.4 Installation de MySQL

```bash
sudo apt install -y mysql-server
sudo mysql_secure_installation
```

Suivre les instructions et définir un mot de passe root fort.

### 1.5 Installation de Nginx

```bash
sudo apt install -y nginx
```

### 1.6 Installation de Redis (optionnel)

```bash
sudo apt install -y redis-server
sudo systemctl enable redis-server
```

### 1.7 Installation de Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
```

## Étape 2: Configuration de la Base de Données

### 2.1 Créer la base de données

```bash
sudo mysql -u root -p
```

```sql
CREATE DATABASE luxestarspower CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'luxeuser'@'localhost' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON luxestarspower.* TO 'luxeuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 2.2 Optimisation MySQL

Éditer `/etc/mysql/mysql.conf.d/mysqld.cnf`:

```ini
[mysqld]
max_connections = 200
innodb_buffer_pool_size = 4G
innodb_log_file_size = 512M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
query_cache_type = 1
query_cache_size = 128M
```

Redémarrer MySQL:
```bash
sudo systemctl restart mysql
```

## Étape 3: Déploiement de l'Application

### 3.1 Créer l'utilisateur de déploiement

```bash
sudo adduser --disabled-password luxedeploy
sudo usermod -aG www-data luxedeploy
```

### 3.2 Cloner le projet

```bash
sudo su - luxedeploy
cd /var/www
git clone https://github.com/votre-org/luxestarspower.git
cd luxestarspower
```

### 3.3 Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 3.4 Configuration

```bash
cp .env.example .env
nano .env
```

Remplir toutes les variables d'environnement. **Important**:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://luxestarspower.com

# Générer une clé forte
APP_KEY=GENERER_UNE_CLE_64_CARACTERES

DB_HOST=localhost
DB_DATABASE=luxestarspower
DB_USERNAME=luxeuser
DB_PASSWORD=VOTRE_MOT_DE_PASSE

# AWS S3
AWS_ACCESS_KEY_ID=votre_key
AWS_SECRET_ACCESS_KEY=votre_secret
AWS_BUCKET=luxestarspower-files
AWS_DEFAULT_REGION=us-east-1

# Stripe
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# PayPal
PAYPAL_MODE=live
PAYPAL_CLIENT_ID=...
PAYPAL_SECRET=...

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=votre_api_key
```

### 3.5 Permissions

```bash
sudo chown -R luxedeploy:www-data /var/www/luxestarspower
sudo chmod -R 755 /var/www/luxestarspower
sudo chmod -R 775 /var/www/luxestarspower/storage
sudo chmod -R 775 /var/www/luxestarspower/public/uploads
```

### 3.6 Exécuter les migrations

```bash
php scripts/migrate.php up
```

### 3.7 Créer l'administrateur

```bash
php scripts/create_admin.php
```

## Étape 4: Configuration Nginx

### 4.1 Créer le fichier de configuration

```bash
sudo nano /etc/nginx/sites-available/luxestarspower.com
```

Contenu:

```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name luxestarspower.com www.luxestarspower.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS Server
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name luxestarspower.com www.luxestarspower.com;
    
    root /var/www/luxestarspower/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/luxestarspower.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/luxestarspower.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers 'ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256';
    ssl_prefer_server_ciphers off;
    
    # HSTS
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    
    # Logs
    access_log /var/log/nginx/luxestarspower-access.log;
    error_log /var/log/nginx/luxestarspower-error.log;
    
    # Client upload size
    client_max_body_size 2G;
    client_body_timeout 300s;
    
    # Gzip
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss;
    
    # Static files cache
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot|webp)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
    
    # Front controller
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP handler
    location ~ \.php$ {
        # Block direct access to PHP files except index.php
        if ($request_uri !~ ^/index\.php) {
            return 301 $scheme://$host$uri;
        }
        
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 300;
    }
    
    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
    
    location ~ ^/(storage|vendor|config|migrations|scripts|tests)/ {
        deny all;
    }
}
```

### 4.2 Activer le site

```bash
sudo ln -s /etc/nginx/sites-available/luxestarspower.com /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Étape 5: SSL avec Let's Encrypt

### 5.1 Installation de Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 5.2 Obtenir le certificat

```bash
sudo certbot --nginx -d luxestarspower.com -d www.luxestarspower.com
```

Suivre les instructions. Le renouvellement automatique est configuré par défaut.

### 5.3 Tester le renouvellement

```bash
sudo certbot renew --dry-run
```

## Étape 6: Configuration PHP-FPM

### 6.1 Optimisation PHP

Éditer `/etc/php/8.1/fpm/php.ini`:

```ini
memory_limit = 512M
upload_max_filesize = 2G
post_max_size = 2G
max_execution_time = 300
max_input_time = 300
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
```

### 6.2 Configuration Pool FPM

Éditer `/etc/php/8.1/fpm/pool.d/www.conf`:

```ini
user = www-data
group = www-data
listen = /var/run/php/php8.1-fpm.sock
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

Redémarrer PHP-FPM:

```bash
sudo systemctl restart php8.1-fpm
```

## Étape 7: Configuration des Workers (Queue)

### 7.1 Créer le worker script

```bash
sudo nano /etc/supervisor/conf.d/luxestarspower-worker.conf
```

Contenu:

```ini
[program:luxestarspower-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/luxestarspower/scripts/queue_worker.php
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=luxedeploy
numprocs=4
redirect_stderr=true
stdout_logfile=/var/www/luxestarspower/storage/logs/worker.log
stopwaitsecs=3600
```

### 7.2 Démarrer les workers

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start luxestarspower-worker:*
```

## Étape 8: Backups Automatiques

### 8.1 Script de backup

Le script est déjà inclus: `scripts/backup.php`

### 8.2 Configuration Cron

```bash
sudo crontab -e -u luxedeploy
```

Ajouter:

```cron
# Backup database daily at 2 AM
0 2 * * * php /var/www/luxestarspower/scripts/backup.php >> /var/www/luxestarspower/storage/logs/backup.log 2>&1

# Queue worker (every minute)
* * * * * php /var/www/luxestarspower/scripts/queue_worker.php >> /dev/null 2>&1

# Clean old logs (weekly)
0 0 * * 0 find /var/www/luxestarspower/storage/logs -name "*.log" -mtime +30 -delete

# Clean expired downloads (daily)
0 3 * * * php /var/www/luxestarspower/scripts/cleanup_downloads.php >> /dev/null 2>&1
```

## Étape 9: Monitoring et Logs

### 9.1 Logrotate

Créer `/etc/logrotate.d/luxestarspower`:

```
/var/www/luxestarspower/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 luxedeploy www-data
    sharedscripts
}
```

### 9.2 Monitoring avec Monit (optionnel)

```bash
sudo apt install -y monit
```

Configuration recommandée pour surveiller:
- Nginx
- PHP-FPM
- MySQL
- Redis
- Disk space
- Memory usage

## Étape 10: Webhooks

### 10.1 Stripe Webhook

Dashboard Stripe → Developers → Webhooks → Add endpoint

URL: `https://luxestarspower.com/webhooks/stripe`

Events à écouter:
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `charge.refunded`

Copier le signing secret dans `.env`

### 10.2 PayPal Webhook

Dashboard PayPal → Developer → My Apps → Your App → Webhooks

URL: `https://luxestarspower.com/webhooks/paypal`

Events:
- `PAYMENT.SALE.COMPLETED`
- `PAYMENT.SALE.REFUNDED`

## Étape 11: Tests Post-Déploiement

### Checklist:

- [ ] Site accessible en HTTPS
- [ ] Redirection HTTP → HTTPS fonctionne
- [ ] Login admin fonctionne
- [ ] Upload de fichier fonctionne
- [ ] Création de produit fonctionne
- [ ] Paiement Stripe (mode test d'abord)
- [ ] Webhooks reçus et traités
- [ ] Download links générés correctement
- [ ] Emails envoyés
- [ ] Queue workers actifs
- [ ] Backups automatiques créés
- [ ] Logs écrits correctement

### Tests de charge (optionnel)

```bash
# Installer Apache Bench
sudo apt install apache2-utils

# Test simple
ab -n 1000 -c 10 https://luxestarspower.com/

# Avec auth
ab -n 100 -c 10 -H "Cookie: session=..." https://luxestarspower.com/compte
```

## Étape 12: Optimisations Performance

### 12.1 OPcache

Déjà configuré dans php.ini (voir Étape 6)

### 12.2 Redis pour cache et sessions

Éditer `.env`:

```env
SESSION_DRIVER=redis
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 12.3 CDN

Configurer un CDN (CloudFront, Cloudflare, BunnyCDN) et mettre à jour:

```env
CDN_URL=https://cdn.luxestarspower.com
```

### 12.4 Database Query Cache

Déjà configuré dans MySQL (voir Étape 2)

## Étape 13: Sécurité Additionnelle

### 13.1 Fail2ban

```bash
sudo apt install -y fail2ban
```

Créer `/etc/fail2ban/jail.local`:

```ini
[nginx-http-auth]
enabled = true

[nginx-noscript]
enabled = true

[nginx-badbots]
enabled = true

[nginx-noproxy]
enabled = true
```

### 13.2 Firewall (UFW)

```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### 13.3 Antivirus (ClamAV)

```bash
sudo apt install -y clamav clamav-daemon
sudo freshclam
sudo systemctl enable clamav-daemon
```

Configurer dans `.env`:

```env
CLAMAV_ENABLED=true
CLAMAV_HOST=localhost
CLAMAV_PORT=3310
```

## Étape 14: CI/CD (optionnel)

### GitHub Actions

Créer `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.HOST }}
          username: luxedeploy
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/luxestarspower
            git pull origin main
            composer install --no-dev --optimize-autoloader
            php scripts/migrate.php up
            sudo systemctl reload php8.1-fpm
            sudo systemctl reload nginx
```

## Étape 15: Maintenance Continue

### Tâches hebdomadaires:
- Vérifier les logs d'erreur
- Vérifier l'espace disque
- Vérifier les backups
- Vérifier les stats de performance

### Tâches mensuelles:
- Mettre à jour les dépendances (`composer update`)
- Nettoyer les anciens backups
- Analyser les rapports d'utilisation
- Optimiser les requêtes lentes

### Mises à jour:

```bash
cd /var/www/luxestarspower
git pull origin main
composer install --no-dev --optimize-autoloader
php scripts/migrate.php up
sudo systemctl reload php8.1-fpm
sudo systemctl reload nginx
```

## Support et Aide

- Documentation: https://docs.luxestarspower.com
- Support: support@luxestarspower.com
- Status: https://status.luxestarspower.com

---

**Félicitations! Votre marketplace est maintenant déployée en production! 🎉**
