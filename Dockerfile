FROM php:8.3-fpm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies, Nginx, Redis & PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    supervisor \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip opcache \
    && pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install NPM dependencies & build frontend assets
RUN npm ci && npm run build

# Copy Nginx server configuration
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Make entrypoint script executable
COPY docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy Supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Web Port (Nginx) and WebSocket Port (Reverb) - Uncommon Ports
EXPOSE 9880 9881

# Define Entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
