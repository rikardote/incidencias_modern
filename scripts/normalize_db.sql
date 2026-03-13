-- 1. Desactivar restricciones y modos estrictos
SET sql_mode = '';
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Eliminar todas las llaves foráneas conocidas (para evitar bloqueos de tipo de dato)
-- Intentamos con todas las posibles que el dump legacy o intentos anteriores puedan tener
ALTER TABLE comentarios DROP FOREIGN KEY IF EXISTS comentarios_employee_id_foreign;
ALTER TABLE comentarios DROP FOREIGN KEY IF EXISTS comentarios_employee_id_foreign;
ALTER TABLE deparment_user DROP FOREIGN KEY IF EXISTS deparment_user_deparment_id_foreign;
ALTER TABLE deparment_user DROP FOREIGN KEY IF EXISTS deparment_user_user_id_foreign;
ALTER TABLE incidencias DROP FOREIGN KEY IF EXISTS incidencias_codigodeincidencia_id_foreign;
ALTER TABLE incidencias DROP FOREIGN KEY IF EXISTS incidencias_employee_id_foreign;
ALTER TABLE incidencias DROP FOREIGN KEY IF EXISTS incidencias_qna_id_foreign;
ALTER TABLE incidencias DROP FOREIGN KEY IF EXISTS incidencias_periodo_id_foreign;
ALTER TABLE employees DROP FOREIGN KEY IF EXISTS employees_condicion_id_foreign;
ALTER TABLE employees DROP FOREIGN KEY IF EXISTS employees_deparment_id_foreign;
ALTER TABLE employees DROP FOREIGN KEY IF EXISTS employees_horario_id_foreign;
ALTER TABLE employees DROP FOREIGN KEY IF EXISTS employees_puesto_id_foreign;

-- 3. Convertir IDs de tablas legacy a BIGINT UNSIGNED
ALTER TABLE users MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE employees MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE deparments MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE qnas MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE codigos_de_incidencias MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE incidencias MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE condiciones MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE horarios MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE puestos MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;
ALTER TABLE jornadas MODIFY id BIGINT UNSIGNED AUTO_INCREMENT;

-- 4. Ajustar columnas de llaves foráneas en tablas de datos
ALTER TABLE comentarios MODIFY employee_id BIGINT UNSIGNED;
ALTER TABLE deparment_user MODIFY user_id BIGINT UNSIGNED;
ALTER TABLE deparment_user MODIFY deparment_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY employee_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY qna_id BIGINT UNSIGNED;
ALTER TABLE incidencias MODIFY codigodeincidencia_id BIGINT UNSIGNED;
ALTER TABLE employees MODIFY condicion_id BIGINT UNSIGNED;
ALTER TABLE employees MODIFY deparment_id BIGINT UNSIGNED;
ALTER TABLE employees MODIFY puesto_id BIGINT UNSIGNED;
ALTER TABLE employees MODIFY horario_id BIGINT UNSIGNED;

-- 5. Normalizar datos específicos (Fix para dumps antiguos)
UPDATE employees SET active = 1 WHERE active = 2 OR active IS NULL;

-- 6. Crear índices de rendimiento (Si no existen)
CREATE INDEX IF NOT EXISTS idx_full_name ON employees (name, father_lastname, mother_lastname);
CREATE INDEX IF NOT EXISTS idx_active ON employees (active);
CREATE INDEX IF NOT EXISTS idx_token ON incidencias (token);
CREATE INDEX IF NOT EXISTS idx_created_at ON incidencias (created_at);
CREATE INDEX IF NOT EXISTS idx_deleted_created ON incidencias (deleted_at, created_at);

-- 7. Reactivar restricciones
SET FOREIGN_KEY_CHECKS = 1;
