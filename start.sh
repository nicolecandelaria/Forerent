#!/bin/bash
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache
php artisan migrate:fresh --force
php artisan config:cache
php artisan storage:link

# Seed in background so supervisord starts immediately
php artisan db:seed --force &

# Start nginx and php-fpm immediately
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf