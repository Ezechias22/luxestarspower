# LuxeStarsPower Marketplace

Marketplace de fichiers numériques premium (ebooks, images, vidéos, cours) avec système de paiement intégré (Stripe, PayPal) et gestion complète des vendeurs.

## 🚀 Fonctionnalités

### Pour les Acheteurs
- Navigation et recherche de produits
- Achat sécurisé (Stripe, PayPal)
- Téléchargements sécurisés avec liens expirables
- Historique d'achats
- Système d'avis et notes
- Multi-langues (FR, EN, ES, DE, IT, PT, AR, ZH)
- Multi-devises

### Pour les Vendeurs
- Upload de fichiers via S3 (signed URLs)
- Gestion de produits (création, édition, versions)
- Dashboard avec statistiques
- Système de payout automatique ou manuel
- Gestion des commandes
- Portefeuille interne

### Pour les Administrateurs
- Dashboard complet avec métriques
- Gestion utilisateurs (suspension, rôles)
- Modération produits
- Gestion des remboursements
- Configuration des commissions
- Rapports et exports
- Logs d'activité
- Mode maintenance

## 📋 Prérequis

- PHP 8.1+
- MySQL 8.0+
- Composer
- Nginx ou Apache
- Compte S3 (AWS, DigitalOcean Spaces, etc.)
- Compte Stripe
- Compte PayPal Business (optionnel)
- Redis (optionnel, pour cache)

## 🛠️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-repo/luxestarspower.git
cd luxestarspower
```

### 2. Installer les dépendances

```bash
composer install --no-dev --optimize-autoloader
```

### 3. Configuration

```bash
cp .env.example .env
```

Éditer `.env` et renseigner:
- Database credentials
- APP_KEY (générer avec: `php -r "echo bin2hex(random_bytes(32));"`)
- JWT_SECRET
- AWS S3 credentials
- Stripe keys
- PayPal credentials
- Mail configuration

### 4. Créer la base de données

```bash
mysql -u root -p
```

```sql
CREATE DATABASE luxestarspower CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'luxeuser'@'localhost' IDENTIFIED BY 'votre_password';
GRANT ALL PRIVILEGES ON luxestarspower.* TO 'luxeuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 5. Exécuter les migrations

```bash
php scripts/migrate.php up
```

### 6. Créer le compte admin initial

```bash
php scripts/create_admin.php
```

Le script vous demandera:
- Nom
- Email
- Mot de passe

Un email de vérification sera envoyé automatiquement.

### 7. Configurer Nginx

```nginx
server {
    listen 80;
    server_name luxestarspower.com www.luxestarspower.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name luxestarspower.com www.luxestarspower.com;
    
    root /var/www/luxestarspower/public;
    index index.php;

    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Client max body size (for uploads)
    client_max_body_size 2G;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Block .php files from being accessed directly
    location ~ \.php$ {
        if ($request_uri !~ ^/index\.php$) {
            return 301 $scheme://$host$request_uri;
        }
        
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    location ~ ^/(storage|vendor|config|migrations|scripts|tests)/ {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Redémarrer Nginx:
```bash
sudo systemctl restart nginx
```

### 8. Permissions

```bash
chmod -R 755 /var/www/luxestarspower
chmod -R 775 storage
chmod -R 775 public/uploads
chown -R www-data:www-data /var/www/luxestarspower
```

### 9. Configurer le Cron pour les jobs

```bash
crontab -e
```

Ajouter:
```
* * * * * php /var/www/luxestarspower/scripts/queue_worker.php >> /dev/null 2>&1
0 2 * * * php /var/www/luxestarspower/scripts/backup.php >> /var/www/luxestarspower/storage/logs/backup.log 2>&1
```

### 10. Tester l'installation

Visiter: `https://luxestarspower.com`

Se connecter en admin: `https://luxestarspower.com/admin`

## 🔐 Sécurité

### Recommandations Production

1. **HTTPS obligatoire** : Certificat SSL/TLS actif
2. **Firewall** : Limiter accès SSH et base de données
3. **Fail2ban** : Protection contre brute force
4. **Backups automatiques** : Quotidiens avec rotation
5. **Monitoring** : Sentry, New Relic ou équivalent
6. **Rate limiting** : Configuré sur Nginx
7. **WAF** : Cloudflare ou équivalent
8. **2FA** : Activé pour admin
9. **Mots de passe** : Politique stricte (min 12 caractères)
10. **Antivirus** : ClamAV pour scanner les uploads

### Configuration Stripe

1. Créer compte Stripe: https://dashboard.stripe.com/register
2. Récupérer les clés API (test puis production)
3. Configurer webhook:
   - URL: `https://luxestarspower.com/webhooks/stripe`
   - Events: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`
4. Copier le secret webhook dans `.env`

### Configuration PayPal

1. Créer compte Business: https://www.paypal.com/businessmanage/
2. Aller dans Developer Dashboard
3. Créer une app REST API
4. Récupérer Client ID et Secret
5. Configurer webhook:
   - URL: `https://luxestarspower.com/webhooks/paypal`
   - Events: `PAYMENT.SALE.COMPLETED`, `PAYMENT.SALE.REFUNDED`

### Configuration S3

**AWS S3:**
```bash
# Créer bucket
aws s3 mb s3://luxestarspower-files --region us-east-1

# Configurer CORS
aws s3api put-bucket-cors --bucket luxestarspower-files --cors-configuration file://cors.json
```

**cors.json:**
```json
{
  "CORSRules": [
    {
      "AllowedOrigins": ["https://luxestarspower.com"],
      "AllowedHeaders": ["*"],
      "AllowedMethods": ["PUT", "POST", "GET", "HEAD"],
      "MaxAgeSeconds": 3000
    }
  ]
}
```

**DigitalOcean Spaces:**
1. Créer Space
2. Générer API key
3. Configurer endpoint dans `.env`: `AWS_ENDPOINT=https://nyc3.digitaloceanspaces.com`

## 📊 Modèle de Revenus

### Commissions
- Par défaut: 20% sur chaque vente
- Configurable dans Admin → Paramètres
- Calcul automatique à chaque transaction

### Frais Additionnels
- Featured products: Prix fixe pour mise en avant
- Abonnements vendeurs: Premium features
- Services de conversion/optimisation

### Payouts Vendeurs
- Seuil minimum: $50 (configurable)
- Automatique via Stripe Connect
- Ou manuel via admin (export CSV)
- Délai: Immédiat après validation admin

## 🌍 Multi-langues

### Langues Supportées
- Français (FR) - par défaut
- Anglais (EN)
- Espagnol (ES)
- Allemand (DE)
- Italien (IT)
- Portugais (PT)
- Arabe (AR)
- Chinois (ZH)

### Ajouter une Langue

1. Créer le dossier: `locales/nouvelle_langue/`
2. Copier `locales/fr/messages.php` comme base
3. Traduire toutes les clés
4. Ajouter la langue dans `.env`:
   ```
   SUPPORTED_LOCALES=fr,en,es,de,it,pt,ar,zh,nouvelle_langue
   ```

### Format des Traductions

`locales/fr/messages.php`:
```php
<?php
return [
    'welcome' => 'Bienvenue',
    'auth' => [
        'login' => 'Connexion',
        'register' => 'Inscription',
        'logout' => 'Déconnexion',
    ],
    'products' => [
        'title' => 'Produits',
        'price' => 'Prix : :amount',
    ],
];
```

Utilisation dans le code:
```php
echo __('welcome'); // Bienvenue
echo __('auth.login'); // Connexion
echo __('products.price', ['amount' => '$99']); // Prix : $99
```

## 💰 Devises Supportées

USD, EUR, GBP, CAD, AUD, JPY, CHF, CNY, AED

Configuration par utilisateur dans compte → paramètres.

## 🧪 Tests

```bash
# Tests unitaires
composer test

# Tests avec couverture
composer test -- --coverage-html coverage/

# Code style
composer cs
```

## 📈 Monitoring & Logs

### Logs
- Application: `storage/logs/app-YYYY-MM-DD.log`
- Erreurs: `storage/logs/error-YYYY-MM-DD.log`
- Webhooks: Table `webhook_logs` en DB
- Activité: Table `activity_logs` en DB

### Métriques à Surveiller
- Temps de réponse API
- Taux d'erreur 4xx/5xx
- Taux de conversion checkout
- CPU/RAM serveur
- Espace disque
- Connexions DB

### Alertes Recommandées
- Webhook failure rate > 5%
- Disk usage > 80%
- CPU usage > 90%
- Failed payments > 10/hour
- Download errors > 5%

## 🔄 Mise à Jour

```bash
# Backup DB
php scripts/backup.php

# Pull changes
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php scripts/migrate.php up

# Clear cache
php scripts/clear_cache.php

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx
```

## 🆘 Rollback

```bash
# Restore DB from backup
php scripts/restore.php --file=storage/backups/backup-YYYY-MM-DD.sql

# Revert to previous commit
git revert HEAD
git push origin main
```

## 📧 Configuration Email

### SMTP (Recommandé pour Production)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre@email.com
MAIL_PASSWORD=mot_de_passe_app
MAIL_ENCRYPTION=tls
```

### Services Email Transactionnels
- **Mailgun** : Facile, fiable
- **SendGrid** : Bon pour volume élevé
- **Amazon SES** : Économique
- **Postmark** : Excellent pour transactions

## 🔧 Maintenance

### Mode Maintenance
```bash
# Activer
php scripts/maintenance.php on

# Désactiver
php scripts/maintenance.php off
```

Ou via admin: Admin → Paramètres → Mode Maintenance

### Backups

**Automatique** (via cron):
- Base de données: Quotidien à 2h du matin
- Fichiers: Hebdomadaire
- Rétention: 30 jours

**Manuel**:
```bash
php scripts/backup.php --full
```

### Optimisation

```bash
# Optimiser autoloader
composer dump-autoload --optimize --no-dev

# Nettoyer cache
php scripts/clear_cache.php

# Optimiser DB
php scripts/optimize_db.php

# Analyser images (créer thumbnails manquantes)
php scripts/process_images.php
```

## 🐛 Dépannage

### Erreur 500
1. Vérifier `storage/logs/app-*.log`
2. Vérifier permissions (775 sur storage)
3. Vérifier configuration `.env`
4. Vérifier connexion DB

### Upload échoue
1. Vérifier `client_max_body_size` dans Nginx
2. Vérifier `upload_max_filesize` et `post_max_size` PHP
3. Vérifier credentials S3
4. Vérifier permissions bucket S3

### Webhooks non reçus
1. Vérifier URL webhook dans Stripe/PayPal dashboard
2. Vérifier logs: `SELECT * FROM webhook_logs ORDER BY created_at DESC`
3. Tester avec Stripe CLI: `stripe trigger payment_intent.succeeded`

### Emails non envoyés
1. Vérifier configuration SMTP dans `.env`
2. Vérifier `storage/logs/mail-*.log`
3. Tester: `php scripts/test_email.php votre@email.com`

## 👥 Support

- Documentation: https://docs.luxestarspower.com
- Email: support@luxestarspower.com
- Discord: https://discord.gg/luxestarspower

## 📄 Licence

Propriétaire - Tous droits réservés © 2024 LuxeStarsPower

## 🤝 Contributeurs

Voir CONTRIBUTING.md pour les guidelines de contribution.

## 📚 Stack Technique

- **Backend**: PHP 8.1+ (PSR-4, PSR-12)
- **Database**: MySQL 8.0+
- **Storage**: AWS S3 / DigitalOcean Spaces
- **CDN**: CloudFront / Bunny CDN
- **Payments**: Stripe, PayPal
- **Email**: SMTP / Mailgun / SendGrid
- **Queue**: Database-based (option Redis)
- **Logging**: Monolog
- **Security**: Argon2, CSRF, Rate limiting
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Server**: Nginx, PHP-FPM

---

## ⚡ Quick Start

```bash
# Install
composer install
cp .env.example .env
# Edit .env avec vos credentials

# Setup DB
php scripts/migrate.php up
php scripts/create_admin.php

# Launch
php -S localhost:8000 -t public/
```

Ouvrir: http://localhost:8000

---

**🎉 Prêt pour la production!**

N'oubliez pas de:
- ✅ Configurer SSL/HTTPS
- ✅ Activer les backups automatiques
- ✅ Configurer monitoring
- ✅ Tester les webhooks en sandbox
- ✅ Activer 2FA pour admin
- ✅ Lire SECURITY.md

**Bon lancement! 🚀**
