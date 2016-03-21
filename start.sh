#!/bin/bash

cd /var/www/html/laravel
/usr/local/bin/composer install --prefer-source --no-interaction
/usr/local/bin/composer update

/usr/sbin/apache2ctl -D FOREGROUND
