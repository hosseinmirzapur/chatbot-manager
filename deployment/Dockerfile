# Use the official PHP image with PHP 8.2 FPM based on Ubuntu
FROM php:8.2-fpm

# Install system dependencies and additional packages for gd and intl extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    git \
    bash \
    libicu-dev \
    zlib1g-dev \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql xml gd intl zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Copy existing application directory permissions
COPY . .

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
