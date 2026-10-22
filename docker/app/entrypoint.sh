#!/bin/sh
# Chuẩn bị Laravel trước khi khởi động PHP-FPM.
set -e

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"
