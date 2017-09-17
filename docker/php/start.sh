#!/bin/bash

cd /var/www

chown www-data:www-data ./zug-zug.ru -R
chmod 0777 ./zug-zug.ru

cd /var/www/zug-zug.ru

cp -n .env.example .env

usermod -u 1000 www-data

composer install

php ./artisan key:generate
php ./artisan migrate --force

/usr/bin/nohup php ./artisan queue:work --queue=high,default > ./storage/logs/queue.log 2>&1 &

php-fpm
