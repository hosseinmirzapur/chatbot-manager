# Use PHP with Apache as the base image
FROM php:8.2-apache AS web

# Add cache-busting ARG to force Docker to rebuild this layer
ARG CACHEBUST=1

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libxml2-dev \
    curl \
    zip \
    unzip \
    git \
    bash \
    libzip-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql xml gd intl zip

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Set Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code with cache-busting
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies in production mode
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
