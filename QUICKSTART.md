# 🚀 QUICKSTART - Luxe Stars Power

## Installation en 5 minutes

### 1. Extraction
```bash
tar -xzf luxestarspower_complete.tar.gz
cd luxestarspower
```

### 2. Dépendances
```bash
composer install
```

### 3. Configuration
```bash
cp .env.example .env
nano .env  # Configurer DB, S3, Stripe, PayPal
```

### 4. Base de données
```bash
mysql -u root -p
CREATE DATABASE luxestarspower CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

php migrations/001_initial_schema.php
```

### 5. Admin
```bash
php scripts/create_admin.php admin@domain.com "Admin Name"
# Sauvegarder le mot de passe affiché !
```

### 6. Serveur web (Nginx)
```bash
sudo cp docker/nginx.conf /etc/nginx/sites-available/luxestarspower
sudo ln -s /etc/nginx/sites-available/luxestarspower /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 7. SSL
```bash
sudo certbot --nginx -d luxestarspower.com
```

## ✅ C'est prêt !

Accéder à:
- Site: https://luxestarspower.com
- Admin: https://luxestarspower.com/admin/login

## 📋 Webhooks à configurer

**Stripe Dashboard:**
- URL: `https://luxestarspower.com/webhooks/stripe`
- Events: `payment_intent.succeeded`

**PayPal Dashboard:**
- URL: `https://luxestarspower.com/webhooks/paypal`
- Events: `PAYMENT.CAPTURE.COMPLETED`

## 🔧 Configuration minimale .env

```
DB_HOST=localhost
DB_NAME=luxestarspower
DB_USER=root
DB_PASS=votre_password

AWS_KEY=votre_key
AWS_SECRET=votre_secret
AWS_BUCKET=votre_bucket
AWS_REGION=us-east-1

STRIPE_PUBLIC_KEY=pk_live_xxx
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

PAYPAL_CLIENT_ID=xxx
PAYPAL_SECRET=xxx
PAYPAL_MODE=live
```

## 🐳 Alternative: Docker

```bash
docker-compose up -d
docker-compose exec php php migrations/001_initial_schema.php
docker-compose exec php php scripts/create_admin.php admin@domain.com "Admin"
```

## 📦 Structure complète livrée

✅ Application PHP/MySQL production-ready
✅ Architecture MVC modulaire avec PSR
✅ URLs propres (0 .php visible)
✅ Multilingue (FR, EN, ES, DE, IT)
✅ Paiements: Stripe + PayPal + Webhooks
✅ Stockage: S3 + téléchargements sécurisés
✅ Sécurité: Argon2ID, CSRF, prepared statements
✅ Admin complet + Dashboard vendeur
✅ Commission automatique
✅ Email notifications
✅ Rate limiting
✅ Activity logs
✅ CI/CD pipeline (GitHub Actions)
✅ Docker support
✅ Makefile commandes
✅ Scripts backup/restore
✅ Documentation complète

## 🎯 Commandes utiles (Makefile)

```bash
make install    # Installer dépendances
make migrate    # Exécuter migrations
make admin      # Créer admin
make backup     # Backup DB
make dev        # Serveur dev (localhost:8000)
make deploy     # Déployer production
```

## 📁 Fichiers clés

- `public/index.php` - Front controller
- `app/Router.php` - Routing URLs propres
- `config/config.php` - Configuration
- `migrations/001_initial_schema.php` - Schema DB
- `docker/nginx.conf` - Config Nginx
- `scripts/create_admin.php` - Création admin

## 🔒 Sécurité implémentée

- Argon2ID password hashing
- Prepared statements (SQL injection proof)
- CSRF tokens
- Rate limiting
- Session security (HttpOnly, Secure, SameSite)
- HTTPS enforced
- Security headers (CSP, HSTS, X-Frame-Options)
- Download tokens (signed, expirable)
- Webhook signature verification
- Activity logging

## 💰 Commission & Revenus

- Commission par défaut: 20% (configurable admin)
- Calcul automatique seller_earnings + platform_fee
- Payouts manuels ou automatiques (Stripe Connect)
- Dashboard revenus en temps réel
- Export CSV pour comptabilité

## 🌍 Multilingue

Changer langue: `?lang=fr` (fr, en, es, de, it)

Ajouter nouvelle langue:
1. Créer `config/lang/xx/main.php`
2. Ajouter 'xx' dans `config.php` → `locales.available`

## 📊 URLs principales

| Route | Description |
|-------|-------------|
| / | Accueil |
| /produits | Catalogue |
| /produit/{slug} | Détail produit |
| /compte | Dashboard user |
| /vendeur/dashboard | Dashboard vendeur |
| /admin/dashboard | Dashboard admin |
| /download/{token} | Téléchargement sécurisé |

## 🆘 Support

Questions: admin@luxestarspower.com

Documentation complète:
- README.md
- DEPLOY.md
- SECURITY.md
- LIVRAISON.md

## ✨ Features avancées disponibles

- Upload multipart pour gros fichiers
- Transcoding vidéo (intégrer worker)
- CDN pour streaming
- Génération miniatures auto
- Recherche fulltext
- Filtres avancés
- Analytics vendeur
- Export données GDPR
- Mode maintenance
- Multi-currency
- Coupons/promotions (structure prête)

---

**🎉 Application 100% production-ready livrée !**

Tout le code est optimisé, sécurisé, testé et documenté.
Prêt à scaler avec load balancing et auto-scaling.
