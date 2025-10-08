#!/bin/sh

# Exit on error
set -e

echo "🚀 Starting PGold Backend..."

# Create .env file from .env.example if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
fi

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
if [ -z "$APP_KEY" ] || ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations first (before clearing cache)
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear any existing cache (after migrations so cache table exists)
echo "🧹 Clearing cache..."
php artisan config:clear
php artisan route:clear  
php artisan view:clear
php artisan cache:clear 2>/dev/null || true

# Don't cache in development/container startup
# Caching will be done by the application when needed
echo "⚡ Application optimized (cache disabled for flexibility)"

echo "✅ Application ready!"

# Execute the main command
exec "$@"
