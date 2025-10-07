#!/bin/sh

# Exit on error
set -e

echo "🚀 Starting PGold Backend..."

# Wait for database to be ready (if using external DB)
# sleep 2

# Create storage directories if they don't exist
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create SQLite database if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    echo "📦 Creating SQLite database..."
    touch database/database.sqlite
    chown www-data:www-data database/database.sqlite
    chmod 664 database/database.sqlite
fi

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Cache configuration
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"

# Execute the main command
exec "$@"
