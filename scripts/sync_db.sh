#!/bin/bash

# Ubicarse siempre en la raíz del proyecto para que las rutas relativas funcionen
BASE_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$BASE_DIR"

# Configuración
DB_CONTAINER="modern_db"
APP_CONTAINER="modern_app"
DB_NAME="sistemas"
DB_USER="root"
DB_PASS="root"

if [ -z "$1" ]; then
    echo "❌ Error: Uso: ./scripts/sync_db.sh ruta/al/archivo.sql"
    exit 1
fi

SQL_FILE=$1

if [ ! -f "$SQL_FILE" ]; then
    echo "❌ Error: El archivo $SQL_FILE no existe en $(pwd)"
    exit 1
fi

# 0. Verificar si los contenedores están corriendo
if [ -z "$(docker ps -q -f name=$DB_CONTAINER)" ]; then
    echo "🐳 Los contenedores no están corriendo. Iniciando Docker Compose..."
    docker compose up -d
    echo "⏳ Esperando a que MySQL esté listo..."
    sleep 10
fi

echo "🚀 Iniciando sincronización de base de datos..."

# 1. Resetear bases de datos
echo "📉 Limpiando bases de datos actuales..."
docker exec $DB_CONTAINER mysql -u $DB_USER -p$DB_PASS -e "DROP DATABASE IF EXISTS $DB_NAME; CREATE DATABASE $DB_NAME; DROP DATABASE IF EXISTS sistemas_chats; CREATE DATABASE sistemas_chats;"

# 2. Importar dump
echo "📥 Importando $SQL_FILE..."
docker exec -i $DB_CONTAINER mysql -u $DB_USER -p$DB_PASS $DB_NAME < "$SQL_FILE"

# 2.5 Eliminar tabla de migraciones legacy para que Laravel no se confunda
echo "🧹 Limpiando historial de migraciones antiguo..."
docker exec $DB_CONTAINER mysql -u $DB_USER -p$DB_PASS $DB_NAME -e "DROP TABLE IF EXISTS migrations;"

# 3. Normalizar IDs y crear índices
echo "🔧 Normalizando estructuras de datos y optimizando índices..."
echo "ℹ️  Este paso puede tardar varios minutos dependiendo del tamaño de la base de datos."
docker exec -i $DB_CONTAINER mysql -u $DB_USER -p$DB_PASS $DB_NAME < scripts/normalize_db.sql

# 4. Correr migraciones de Laravel
echo "🏃 Corriendo migraciones de Laravel..."
docker exec $APP_CONTAINER php artisan migrate --force

# 5. Importar datos adicionales del QNA (CURP/RFC)
if [ -f "QNA04.csv" ]; then
    echo "📄 Enriqueciendo empleados con datos de QNA04.csv..."
    docker exec $APP_CONTAINER php scripts/import_qna_data.php
else
    echo "⚠️ QNA04.csv no encontrado en la raíz, saltando importación de CURP/RFC."
fi

# 6. Limpiar cachés
echo "🧹 Limpiando cachés de la aplicación..."
docker exec $APP_CONTAINER php artisan optimize:clear

echo "✅ Sincronización completada con éxito."
