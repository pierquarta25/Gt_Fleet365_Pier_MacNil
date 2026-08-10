#!/usr/bin/env bash

# Ottimizzazione cache di Laravel per l'ambiente di produzione
echo "Ottimizzazione cache di Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verifica connessione al database prima delle migrazioni
echo "Verifica connessione al database..."
php artisan db:monitor --max=1 2>&1 || echo "⚠️  ATTENZIONE: Connessione al database fallita. Verificare DATABASE_URL."

# Esecuzione automatica delle migrazioni del database
echo "Esecuzione delle migrazioni del database..."
php artisan migrate --force

# Avvio del server Apache in foreground
echo "Avvio di Apache..."
exec apache2-foreground
