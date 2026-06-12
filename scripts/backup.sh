#!/bin/bash

FECHA=$(date +"%Y-%m-%d_%H-%M-%S")

DB_NAME="turinghelpdesk"
DB_USER="turingadmin"
DB_PASS="TuringAsir2026*"

BACKUP_DIR="/var/backups/turinghelpdesk"
BACKUP_FILE="$BACKUP_DIR/backup_$DB_NAME_$FECHA.sql"

mysqldump --no-tablespaces -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
	echo "Backup creado correctamente: $BACKUP_FILE"
else
	echo "Error al crear el backup"
fi
