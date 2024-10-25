# Use the official PHP image with PHP 8.2
FROM php:8.2-fpm-alpine


# Install system dependencies
RUN apk update && apk add --no-cache \
    libpng-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    git \
    bash \
    icu-dev \
    zlib-dev \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql xml gd intl zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Set working directory
WORKDIR /var/www/chatbots/backend

# Copy existing application directory permissions
COPY --chown=www-data:www-data . .

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
RUN php artisan key:generate

# Expose port 9000 to the outside world
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
