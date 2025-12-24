FROM php:8.2-cli-alpine

# Installation des dépendances système
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    mysql-client \
    icu-dev \
    oniguruma-dev

# Installation des extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        intl \
        mbstring \
        exif \
        opcache \
        bcmath

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration PHP avec limites d'upload augmentées
RUN echo 'upload_max_filesize = 100M' > /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'post_max_size = 100M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'memory_limit = 512M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_execution_time = 600' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_input_time = 600' >> /usr/local/etc/php/conf.d/uploads.ini

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers
COPY . /app

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app

# Exposer le port
EXPOSE 8080

# Démarrer le serveur PHP built-in
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]