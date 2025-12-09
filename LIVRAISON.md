# Luxe Stars Power - Livraison complète

## Contenu de la livraison

✅ Application PHP/MySQL production-ready
✅ Architecture MVC modulaire
✅ URLs propres (sans .php visible)
✅ Système multilingue (FR, EN, ES, DE, IT)
✅ Paiements Stripe + PayPal avec webhooks
✅ Stockage S3 + téléchargements sécurisés
✅ Commission automatique configurable
✅ Interface admin complète
✅ Dashboard vendeur
✅ Sécurité: Argon2ID, prepared statements, CSRF
✅ Migrations DB versionnées
✅ Script création admin sécurisé
✅ Documentation déploiement

## Démarrage rapide

1. **Extraire l'archive**
```bash
tar -xzf luxestarspower.tar.gz
cd luxestarspower
```

2. **Installer dépendances**
```bash
composer install
```

3. **Configuration**
```bash
cp .env.example .env
# Éditer .env avec vos credentials
```

4. **Base de données**
```bash
mysql -u root -p
CREATE DATABASE luxestarspower CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;

php migrations/001_initial_schema.php
```

5. **Nginx** 
```bash
cp docker/nginx.conf /etc/nginx/sites-available/luxestarspower
ln -s /etc/nginx/sites-available/luxestarspower /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

6. **Créer admin**
```bash
php scripts/create_admin.php admin@luxestarspower.com "Admin Name"
# Sauvegarder le mot de passe affiché
```

7. **SSL**
```bash
certbot --nginx -d luxestarspower.com
```

## Webhooks à configurer

**Stripe Dashboard**
- URL: https://luxestarspower.com/webhooks/stripe
- Secret: voir dashboard Stripe
- Events: payment_intent.succeeded

**PayPal Dashboard**
- URL: https://luxestarspower.com/webhooks/paypal
- Events: PAYMENT.CAPTURE.COMPLETED

## Variables .env requises

```
DB_HOST=localhost
DB_NAME=luxestarspower
DB_USER=root
DB_PASS=your_password

AWS_KEY=your_aws_key
AWS_SECRET=your_aws_secret
AWS_BUCKET=your_bucket
AWS_REGION=us-east-1

STRIPE_PUBLIC_KEY=pk_live_xxx
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

PAYPAL_CLIENT_ID=xxx
PAYPAL_SECRET=xxx
PAYPAL_MODE=live
```

## Structure fichiers

```
luxestarspower/
├── app/                      # Code application
│   ├── Controllers/          # MVC controllers
│   │   ├── Admin/           # Controllers admin
│   │   └── Seller/          # Controllers vendeur
│   ├── Models/              # Modèles de données
│   ├── Repositories/        # Accès DB sécurisé
│   ├── Services/            # Logique métier
│   │   ├── AuthService.php
│   │   ├── PaymentService.php
│   │   ├── StorageService.php
│   │   └── DownloadService.php
│   ├── Database.php         # Connexion DB
│   ├── Router.php           # Routing URLs propres
│   └── I18n.php             # Système multilingue
│
├── config/                   # Configuration
│   ├── config.php           # Config principale
│   └── lang/                # Traductions
│       ├── fr/, en/, es/, de/, it/
│
├── migrations/               # Migrations DB
│   └── 001_initial_schema.php
│
├── public/                   # Document root web
│   ├── index.php            # Front controller
│   └── assets/
│       ├── css/main.css     # Styles modernes
│       └── js/main.js
│
├── views/                    # Templates
│   ├── layout.php           # Layout principal
│   ├── front/               # Vues publiques
│   ├── seller/              # Vues vendeur
│   ├── admin/               # Vues admin
│   └── errors/              # Pages erreur
│
├── scripts/                  # Scripts admin
│   └── create_admin.php     # Création admin sécurisé
│
├── docker/                   # Configuration serveur
│   └── nginx.conf           # Config Nginx
│
├── .env.example             # Template variables env
├── composer.json            # Dépendances PHP
├── README.md                # Documentation
├── DEPLOY.md                # Guide déploiement
└── SECURITY.md              # Sécurité

```

## URLs principales

| URL | Description |
|-----|-------------|
| / | Page d'accueil |
| /produits | Catalogue produits |
| /produit/{slug} | Détail produit |
| /login | Connexion |
| /register | Inscription |
| /compte | Dashboard utilisateur |
| /compte/achats | Historique achats |
| /compte/telechargements | Téléchargements |
| /vendre | Devenir vendeur |
| /vendeur/dashboard | Dashboard vendeur |
| /vendeur/produits | Gestion produits |
| /vendeur/produit/nouveau | Ajouter produit |
| /admin/login | Connexion admin |
| /admin/dashboard | Dashboard admin |
| /admin/users | Gestion utilisateurs |
| /admin/products | Gestion produits |
| /admin/orders | Gestion commandes |
| /admin/settings | Paramètres |
| /download/{token} | Téléchargement sécurisé |

## Changement de langue

Ajouter `?lang=fr` à n'importe quelle URL:
- FR: ?lang=fr
- EN: ?lang=en
- ES: ?lang=es
- DE: ?lang=de
- IT: ?lang=it

## Fonctionnalités clés

### Paiements
- Stripe et PayPal intégrés
- Webhooks sécurisés vérifiés
- Commission automatique (20% par défaut, configurable)
- Calcul seller_earnings + platform_fee

### Sécurité
- Passwords: Argon2ID hashing
- SQL: Prepared statements (injection protection)
- Sessions: HttpOnly, Secure, SameSite
- CSRF: Tokens sur formulaires
- Rate limiting: Login attempts
- HTTPS: TLS 1.2+, HSTS headers
- Downloads: Liens signés temporaires

### Téléchargements
- Génération tokens uniques
- Expiration 24h par défaut
- Liens signés S3
- Tracking IP + user agent
- Logs complets

### Admin
- Stats en temps réel
- Gestion utilisateurs (ban, promote)
- Modération produits (activer/désactiver, featured)
- Gestion commandes (refunds)
- Payouts management
- Configuration commission
- Activity logs

### Vendeur
- Upload produits (ebooks, vidéos, images, formations)
- Gestion prix + devise
- Statistiques ventes
- Historique revenus
- Dashboard complet

## Backups recommandés

```bash
# Ajouter à crontab
0 2 * * * mysqldump -u user -p luxestarspower | gzip > /backups/db_$(date +\%Y\%m\%d).sql.gz
0 3 * * * tar -czf /backups/files_$(date +\%Y\%m\%d).tar.gz /var/www/luxestarspower/storage
```

## Monitoring

- Logs application: storage/logs/
- Logs Nginx: /var/log/nginx/
- Logs PHP: /var/log/php-fpm/
- Métriques: admin dashboard

## Support

Pour questions techniques ou assistance:
- Email: admin@luxestarspower.com
- Documentation: README.md, DEPLOY.md, SECURITY.md

## Checklist post-déploiement

- [ ] DB migrations exécutées
- [ ] Admin créé et mot de passe sauvegardé
- [ ] .env configuré avec toutes les variables
- [ ] S3 bucket créé et credentials configurés
- [ ] Stripe configuré (keys + webhooks)
- [ ] PayPal configuré
- [ ] SSL activé (HTTPS)
- [ ] Nginx config active
- [ ] Permissions fichiers correctes (755 storage/)
- [ ] Cron backups configurés
- [ ] Webhooks testés (Stripe + PayPal)
- [ ] Upload produit test réussi
- [ ] Paiement test réussi
- [ ] Téléchargement sécurisé testé

## Notes finales

- Tous les fichiers .php sont masqués (URLs propres)
- Redirections 301 automatiques pour anciennes URLs
- Système multilingue extensible
- Commission configurable via admin settings
- Payouts manuels ou automatisables via Stripe Connect
- Architecture scalable prête pour load balancing
- CDN ready pour assets
- Transcoding vidéo prévu (intégrer worker)

Projet complet production-ready livré!
