#!/bin/bash

# Script para importar base de datos sistemas legacy a estructura moderna
# Uso: ./importar_sistemas.sh archivo.sql

SQL_FILE=$1

if [ -z "$SQL_FILE" ]; then
    echo "Uso: ./importar_sistemas.sh nombre_archivo.sql"
    exit 1
fi

if [ ! -f "$SQL_FILE" ]; then
    echo "Error: El archivo $SQL_FILE no existe."
    exit 1
fi

echo "🚀 Iniciando proceso de importación para $SQL_FILE..."

# 1. Limpiar tablas existentes para evitar conflictos
echo "🧹 Limpiando base de datos 'sistemas'..."
docker exec modern_db mysql -uroot -proot sistemas -e "
SET FOREIGN_KEY_CHECKS=0;
SET sql_mode = '';
DROP TABLE IF EXISTS qnas, deparments, deparment_user, puestos, horarios, jornadas, condiciones, employees, codigos_de_incidencias, periodos, incidencias, users, cache, cache_locks, jobs, job_batches, failed_jobs, sessions, password_reset_tokens, migrations, conversations, messages, comentarios, configurations, checadas, capture_exceptions, scaffoldinterfaces;
SET FOREIGN_KEY_CHECKS=1;"

# 2. Importar el archivo SQL
echo "📥 Importando datos (esto puede tardar unos segundos)..."
docker exec -i modern_db mysql -u rishar -psecretpassword sistemas -e "SET FOREIGN_KEY_CHECKS=0; SET sql_mode = ''; SOURCE /dev/stdin; SET FOREIGN_KEY_CHECKS=1;" < "$SQL_FILE"

# 3. Modernizar estructura de IDs (Compatibilidad Laravel 11 BigInt)
echo "⚡ Modernizando estructura de datos (BigInt IDs)..."
docker exec modern_db mysql -uroot -proot sistemas -e "
SET FOREIGN_KEY_CHECKS=0;
SET sql_mode = '';
ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE deparments MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE employees MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE qnas MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE codigos_de_incidencias MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE deparment_user MODIFY user_id BIGINT UNSIGNED;
ALTER TABLE deparment_user MODIFY deparment_id BIGINT UNSIGNED;
ALTER TABLE employees MODIFY deparment_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY employee_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY qna_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY codigodeincidencia_id BIGINT UNSIGNED;
ALTER TABLE comentarios MODIFY employee_id BIGINT UNSIGNED;
SET FOREIGN_KEY_CHECKS=1;"

# 4. Forzar migraciones modernas de Laravel
# Re-creamos la tabla de migraciones y marcamos las que ya existen por los datos importados
echo "✅ Sincronizando migraciones de Laravel..."
docker exec modern_db mysql -u rishar -psecretpassword sistemas -e "
CREATE TABLE IF NOT EXISTS migrations (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, migration VARCHAR(255), batch INT);
TRUNCATE migrations;
INSERT INTO migrations (migration, batch) VALUES 
('0001_01_01_000000_create_users_table', 1),
('2025_01_01_000000_create_legacy_tables', 1),
('2026_02_23_222712_add_username_to_users_table', 1),
('2026_02_28_222900_sync_manual_db_changes', 1);"

# Ejecutar el comando de migración para crear tablas de sistema (sessions, cache, etc.)
docker exec modern_app php artisan migrate --force

# 5. Limpiar caché de la app
echo "🧹 Limpiando caché de la aplicación..."
docker exec modern_app php artisan config:clear
docker exec modern_app php artisan cache:clear

echo "✨ Proceso completado exitosamente. La base de datos está actualizada y modernizada."
