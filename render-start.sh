#!/usr/bin/env bash

# Ottimizzazione cache di Laravel per l'ambiente di produzione
echo "Ottimizzazione cache di Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Esecuzione automatica delle migrazioni del database
echo "Esecuzione delle migrazioni del database..."
php artisan migrate --force

# Avvio del server Apache in foreground
echo "Avvio di Apache..."
exec apache2-foreground
