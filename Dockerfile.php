FROM php:8.2-fpm-alpine

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

# Installation de Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration PHP
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configuration personnalisée
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

# Définir le répertoire de travail
WORKDIR /var/www/luxestarspower

# Permissions
RUN chown -R www-data:www-data /var/www/luxestarspower \
    && chmod -R 755 /var/www/luxestarspower

# Exposer le port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
