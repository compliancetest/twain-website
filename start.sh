#!/bin/bash

chmod -R 777 /var/www/html/laravel/storage/logs
chmod -R 777 /var/www/html/laravel/bootstrap/cache
chmod -R 777 /var/www/html/laravel/storage/framework/views
chmod -R 777 /var/www/html/laravel/storage/framework/sessions
chmod -R 777 /var/www/html/laravel/storage/app/public/transactions/

#this command used to run queue listener in background
nohup php /var/www/html/laravel/artisan queue:listen --timeout=300 > /dev/null 2>&1 &

cd /var/www/html/laravel && php artisan migrate --force


/usr/sbin/apache2ctl -D FOREGROUND
