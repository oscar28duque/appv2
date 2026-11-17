#!/bin/sh
set -e

# Si se usa SQLite, asegurarse de que el archivo exista
if [ "$DB_CONNECTION" = "sqlite" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
fi

# Optimizaciones de Laravel y migración automática
php artisan storage:link || true
php artisan migrate --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Iniciar PHP-FPM en segundo plano y Nginx en primer plano
php-fpm -D
exec nginx -g "daemon off;"
