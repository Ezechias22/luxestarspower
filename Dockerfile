FROM php:8.2-cli-alpine

# Install dependencies
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        zip \
        intl \
        opcache \
        mbstring

# MODIFIÉ : Augmente les limites à 500MB
RUN echo 'upload_max_filesize = 500M' > /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'post_max_size = 500M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'memory_limit = 1024M' >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo 'max_execution_time = 900' >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /app

COPY . .

# Install Composer dependencies
RUN if [ -f composer.json ]; then \
        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
        composer install --no-dev --optimize-autoloader; \
    fi

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]