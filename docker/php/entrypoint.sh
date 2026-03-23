#!/bin/bash
set -e

# Configurar zona horaria del sistema
export TZ=America/Tijuana
ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Limpiar y optimizar caché para producción
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# Iniciar Reverb en segundo plano con logging
echo "Iniciando Reverb..."
php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &

# Iniciar Monitor de Biométricos en segundo plano
# (DESHABILITADO por ADMS)
echo "Iniciando Monitor de Biométricos (Real-time)... [POLLING DESACTIVADO - USANDO ADMS]"
# php artisan biometrico:monitor > storage/logs/biometrico_monitor.log 2>&1 &

# Iniciar el proceso principal
if [ "$#" -gt 0 ]; then
    echo "Ejecutando comando: $@"
    exec "$@"
else
    echo "Iniciando PHP-FPM..."
    exec php-fpm
fi
