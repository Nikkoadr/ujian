#!/bin/sh
set -e

echo "Running Laravel setup..."

php artisan storage:link --force || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Starting Octane..."

exec php artisan octane:start \
    --server=frankenphp \
    --host=0.0.0.0 \
    --port=8000 \
    --workers=2 \
    --max-requests=300