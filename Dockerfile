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
COPY . .
RUN chown -R www-data:www-data /var/www/chatbots/backend \
    && chmod -R 775 /var/www/chatbots/backend/storage \
    && chmod -R 775 /var/www/chatbots/backend/bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN chown -R www-data:www-data /var/www/chatbots/backend/vendor \
    && chmod -R 775 /var/www/chatbots/backend/vendor

# Expose port 9000 to the outside world
EXPOSE 9000

# Start PHP-FPM server
CMD ["php-fpm"]
