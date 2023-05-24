#!/bin/sh

sleep 10s

chown -R www-data:www-data storage
php artisan migrate

# Start Apache server
apache2-foreground
