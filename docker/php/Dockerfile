FROM php:7.4-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    $PHPIZE_DEPS \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libmcrypt-dev

# Install mcrypt (required by legacy CodeIgniter Encrypt library)
RUN pecl install mcrypt-1.0.4 \
    && docker-php-ext-enable mcrypt

# Install redis extension (required by CI3 redis session driver)
RUN pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd mysqli zip soap

# Clear build deps & cache
RUN apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/zz-uploads.ini

WORKDIR /var/www/html
