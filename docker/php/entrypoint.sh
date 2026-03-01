#!/bin/bash
set -e

# Limpiar y optimizar caché para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Reverb en segundo plano (Host 0.0.0.0 para que escuche fuera del contenedor)
php artisan reverb:start --host=0.0.0.0 --port=8080 &

# Iniciar el proceso principal (PHP-FPM)
exec php-fpm
