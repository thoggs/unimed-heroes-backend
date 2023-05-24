FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Set environment variables
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set timezone
RUN echo "UTC" > /etc/timezone

# Install dependencies
RUN apt update && apt install -y \
    git \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    zip \
    zlib1g-dev \
    && docker-php-ext-install intl opcache pdo pdo_pgsql pgsql zip

# Install Node.js and NPM
RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
RUN apt-get install -y nodejs

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project files
COPY . .

# Install dependencies with Composer
RUN composer install --optimize-autoloader --no-dev

# Copy configuration files
RUN cp .env.example .env

# Cache routes
RUN php artisan route:cache

# Enable Apache modules
RUN a2enmod rewrite

# Configure Laravel logs
RUN touch /var/www/html/storage/logs/laravel.log
RUN ln -sf /dev/stdout storage/logs/laravel.log
RUN chmod -R 777 /var/www

# Copy entrypoint script and make it executable
COPY .docker/scripts/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Set the entrypoint to execute the script
CMD ["/usr/local/bin/entrypoint.sh"]
