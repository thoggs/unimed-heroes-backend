#!/bin/sh

sleep 10s

chown -R www-data:www-data storage

php artisan migrate

php artisan marvel:fetch-heroes

php artisan schedule:run

# Start Apache server
apache2-foreground
