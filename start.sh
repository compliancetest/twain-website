#!/bin/bash

chmod -R 777 /var/www/html/laravel/storage/logs
chmod -R 777 /var/www/html/laravel/bootstrap/cache
chmod -R 777 /var/www/html/laravel/storage/framework/views
chmod -R 777 /var/www/html/laravel/storage/framework/sessions
chmod -R 777 /var/www/html/laravel/storage/app/public/transactions/


#this command used to run queue listener in background
nohup php /var/www/html/laravel/artisan queue:listen > /dev/null 2>&1 &


/usr/sbin/apache2ctl -D FOREGROUND
