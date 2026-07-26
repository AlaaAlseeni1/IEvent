#!/bin/bash
set -e

cd /var/www/html

echo "Preparing storage directories..."
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         storage/app/public \
         bootstrap/cache

echo "Running migrations..."
php artisan migrate --force 2>&1 || true

echo "Running seeders..."
php artisan db:seed --force 2>&1 || true

echo "Linking storage (for uploaded logos/images)..."
php artisan storage:link --force 2>&1 || true

echo "Caching config..."
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

# مهم: أوامر artisan أعلاه تعمل كـ root، لذا نعيد ملكية storage و bootstrap/cache
# إلى www-data (مستخدم php-fpm) وإلا يفشل كتابة الكاش/الجلسات بخطأ Permission denied.
echo "Fixing storage permissions for www-data..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
