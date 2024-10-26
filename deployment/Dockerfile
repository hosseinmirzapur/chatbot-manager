# Use the official PHP image with PHP 8.2 FPM
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zlib1g-dev \
    libzip-dev \
    unzip \
    libonig-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql xml gd intl zip mbstring openssl bcmath fileinfo

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
