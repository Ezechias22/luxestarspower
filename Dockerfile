FROM php:8.2-cli

WORKDIR /app

# Extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copier tout
COPY . .

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer dépendances
RUN composer install --no-dev --optimize-autoloader

# Port
EXPOSE 8080

# Démarrer
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t public"]