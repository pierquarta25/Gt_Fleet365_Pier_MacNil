# Stage 1: Compilazione degli asset React/Vite
FROM node:20-alpine AS build-frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Ambiente PHP di Produzione
FROM php:8.4-apache

# Installazione delle dipendenze di sistema ed estensioni PHP per PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Abilitazione del modulo rewrite di Apache per gestire le rotte di Laravel
RUN a2enmod rewrite

# Copia della configurazione di Apache personalizzata per la DocumentRoot
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copia di Composer dall'immagine ufficiale
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia del codice sorgente del progetto
COPY . .

# Copia degli asset frontend già compilati dallo Stage 1
COPY --from=build-frontend /app/public/build ./public/build

# Installazione delle dipendenze di Composer per la produzione
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurazione dei permessi per le cartelle di storage e cache di Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Esposizione della porta 80
EXPOSE 80

# Avvio tramite script render-start.sh
RUN chmod +x /var/www/html/render-start.sh
ENTRYPOINT ["/var/www/html/render-start.sh"]
