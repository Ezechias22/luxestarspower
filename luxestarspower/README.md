# Luxe Stars Power - Marketplace Premium

Marketplace production-ready pour produits numériques.

## Features
- Multilingue (FR, EN, ES, DE, IT)
- URLs propres SEO-friendly
- Paiements Stripe + PayPal
- S3 + CDN
- Téléchargements sécurisés
- Admin complet
- Commission automatique

## Installation
```bash
composer install
cp .env.example .env
php migrations/001_initial_schema.php
php scripts/create_admin.php admin@domain.com "Admin"
```

Voir DEPLOY.md pour détails.

## Stack
PHP 8.1+ | MySQL 8+ | Nginx | S3 | Stripe | PayPal

## License
Propriétaire
