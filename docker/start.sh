#!/bin/bash
set -e

echo "Running migrations..."
php /var/www/html/artisan migrate --force 2>&1 || true

echo "Running seeders..."
php /var/www/html/artisan db:seed --force 2>&1 || true

echo "Linking storage (for uploaded logos/images)..."
php /var/www/html/artisan storage:link --force 2>&1 || true

echo "Caching config..."
php /var/www/html/artisan config:cache 2>&1 || true
php /var/www/html/artisan route:cache 2>&1 || true
php /var/www/html/artisan view:cache 2>&1 || true

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
