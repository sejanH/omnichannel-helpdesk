#!/bin/bash
set -e

# Ensure Composer dependencies exist
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Ensure .env file exists for Laravel console commands
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from .env.example..."
    if [ -f /var/www/html/.env.example ]; then
        cp /var/www/html/.env.example /var/www/html/.env
    else
        touch /var/www/html/.env
    fi
fi

# Ensure SQLite database file exists if using sqlite
if [ "$DB_CONNECTION" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Wait for MySQL database connection if using mysql
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL database ($DB_HOST:$DB_PORT)..."
    max_tries=30
    count=0
    until php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Throwable \$e) { exit(1); }" 2>/dev/null; do
        count=$((count+1))
        if [ $count -ge $max_tries ]; then
            echo "Error: MySQL database connection timed out."
            exit 1
        fi
        sleep 2
    done
    echo "MySQL connection established successfully!"
fi

# Ensure storage directories exist and have proper permissions for Nginx & PHP-FPM
mkdir -p /var/www/html/storage/framework/{cache,sessions,views}
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generate APP_KEY if not already set or empty in .env
if [ -z "$APP_KEY" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi

# Create storage symlink
php artisan storage:link --force || true

# Run database migrations and seeder automatically
echo "Running database migrations & seeders..."
php artisan migrate --force
php artisan db:seed --force

# Optimize Laravel cache for production
echo "Caching Laravel configuration & routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "OmniDesk Application (Nginx + PHP-FPM + MySQL + Reverb) is ready! Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
