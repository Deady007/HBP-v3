FROM php:8.4-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip \
    libzip-dev libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage

# Copy .env.example to .env if it doesn't exist
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Run artisan setup commands automatically
CMD php artisan config:cache && \
    php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=8000
