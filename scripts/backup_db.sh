#!/bin/bash
# Script de backup automatique de la base de données
# Usage: ./scripts/backup_db.sh

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/backups/luxestarspower"
DB_NAME="luxestarspower"
DB_USER="root"
DB_PASS=""

# Créer le dossier de backup s'il n'existe pas
mkdir -p $BACKUP_DIR

# Backup avec compression
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/db_backup_$TIMESTAMP.sql.gz

# Garder seulement les 30 derniers backups
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +30 -delete

echo "Backup completed: db_backup_$TIMESTAMP.sql.gz"

# Log
echo "$(date): Backup completed" >> $BACKUP_DIR/backup.log
