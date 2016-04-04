#!/bin/bash


chmod -R 777 /var/www/html/laravel/storage/logs
chmod -R 777 /var/www/html/laravel/bootstrap/cache
chmod -R 777 /var/www/html/laravel/storage/framework/views
chmod -R 777 /var/www/html/laravel/storage/framework/sessions

/usr/sbin/apache2ctl -D FOREGROUND
