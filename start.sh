#!/bin/bash
php artisan migrate --force
php artisan config:cache
php artisan storage:link
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf