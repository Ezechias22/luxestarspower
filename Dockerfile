FROM php:8.2-cli-alpine

# Installation des dépendances système
RUN apk add --no-cache \
    git curl libpng-dev libjpeg-turbo-dev libwebp-dev \
    freetype-dev libzip-dev zip unzip mysql-client \
    icu-dev oniguruma-dev

# Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip intl mbstring opcache

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# CONFIGURATION PHP AVEC LIMITES AUGMENTÉES
RUN echo 'upload_max_filesize = 100M' > /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_execution_time = 600' >> /usr/local/etc/php/conf.d/uploads.ini

# Copier application
WORKDIR /app
COPY . /app

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader

# Exposer port
EXPOSE 8080

# Démarrer serveur
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]