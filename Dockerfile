FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg62-turbo-dev libfreetype6-dev zip git curl unzip \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy Laravel app
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Install Caddy (web server)
# Install Caddy (production static build)
RUN curl -o /usr/bin/caddy -fsSL "https://github.com/caddyserver/caddy/releases/latest/download/caddy_linux_amd64" \
  && chmod +x /usr/bin/caddy


# Copy Caddy config
COPY Caddyfile /etc/Caddyfile

EXPOSE 10000

CMD ["caddy", "run", "--config", "/etc/Caddyfile"]
