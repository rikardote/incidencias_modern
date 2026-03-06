#!/bin/bash
set -e

# Limpiar y optimizar caché para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Iniciar Reverb en segundo plano con logging
echo "Iniciando Reverb..."
php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &

# Iniciar Monitor de Biométricos en segundo plano
# (DESHABILITADO) Para habilitarlo nuevamente, descomenta la siguiente línea y reinicia el contenedor
echo "Iniciando Monitor de Biométricos (Real-time)... [MODO MANUAL: DESACTIVADO]"
# php artisan biometrico:monitor > storage/logs/biometrico_monitor.log 2>&1 &

# Iniciar el proceso principal (PHP-FPM)
echo "Iniciando PHP-FPM..."
exec php-fpm
