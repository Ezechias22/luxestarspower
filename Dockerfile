FROM php:8.2-cli

WORKDIR /app

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Installer extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    gd \
    zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier composer.json et composer.lock d'abord
COPY composer.json composer.lock* ./

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copier le reste des fichiers
COPY . .

# Créer répertoires nécessaires
RUN mkdir -p storage/logs storage/sessions storage/cache \
    && chmod -R 775 storage

# Port
EXPOSE 8080

# Démarrer
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]