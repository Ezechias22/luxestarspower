# Sécurité

## Mesures implémentées

- **Authentification**: Argon2ID hashing
- **SQL**: Prepared statements (PDO)
- **Sessions**: HttpOnly, Secure, SameSite
- **CSRF**: Tokens sur formulaires
- **Rate limiting**: Login, webhooks
- **HTTPS**: TLS 1.2+, HSTS
- **Headers**: CSP, X-Frame-Options
- **Downloads**: Liens signés expirables
- **Webhooks**: Signature verification
- **Logs**: Activity audit trail

## Variables sensibles

Jamais commiter:
- .env
- credentials
- keys privées

Utiliser secrets manager en production.

## Backups

- DB: quotidien off-site
- Fichiers: hebdomadaire
- Rotation: 30 jours

## Reporting

Vulnérabilités: security@luxestarspower.com
