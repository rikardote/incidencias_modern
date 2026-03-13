-- 1. Desactivar restricciones y modos estrictos
SET sql_mode = '';
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Eliminar llaves foráneas antiguas de forma segura (Compatible con MySQL 8.0)
-- En MySQL 8.0 no existe "DROP FOREIGN KEY IF EXISTS", por lo que usamos un SP.
DELIMITER //
CREATE PROCEDURE DropForeignKeyIfExists(
    IN tableName VARCHAR(255),
    IN constraintName VARCHAR(255)
)
BEGIN
    IF EXISTS (
        SELECT NULL 
        FROM information_schema.table_constraints 
        WHERE table_schema = DATABASE() 
        AND table_name = tableName 
        AND constraint_name = constraintName 
        AND constraint_type = 'FOREIGN KEY'
    ) THEN
        SET @query = CONCAT('ALTER TABLE ', tableName, ' DROP FOREIGN KEY ', constraintName);
        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL DropForeignKeyIfExists('comentarios', 'comentarios_employee_id_foreign');
CALL DropForeignKeyIfExists('deparment_user', 'deparment_user_deparment_id_foreign');
CALL DropForeignKeyIfExists('deparment_user', 'deparment_user_user_id_foreign');

DROP PROCEDURE IF EXISTS DropForeignKeyIfExists;

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
UPDATE employees 
SET active = CASE 
    WHEN deparment_id = 33 THEN 0
    ELSE 1 
END
WHERE deparment_id = 33 OR active = 2 OR active IS NULL;

-- 6. Crear índices de rendimiento de forma segura (Compatible con MySQL 8.0)
-- En MySQL 8.0 no existe CREATE INDEX IF NOT EXISTS.
DELIMITER //
CREATE PROCEDURE CreateIndexIfNotExists(
    IN tableName VARCHAR(255),
    IN indexName VARCHAR(255),
    IN columns VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT NULL 
        FROM information_schema.statistics 
        WHERE table_schema = DATABASE() 
        AND table_name = tableName 
        AND index_name = indexName
    ) THEN
        SET @query = CONCAT('CREATE INDEX ', indexName, ' ON ', tableName, ' (', columns, ')');
        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

CALL CreateIndexIfNotExists('employees', 'idx_full_name', 'name, father_lastname, mother_lastname');
CALL CreateIndexIfNotExists('employees', 'idx_active', 'active');
CALL CreateIndexIfNotExists('incidencias', 'idx_token', 'token');
CALL CreateIndexIfNotExists('incidencias', 'idx_created_at', 'created_at');
CALL CreateIndexIfNotExists('incidencias', 'idx_deleted_created', 'deleted_at, created_at');

DROP PROCEDURE IF EXISTS CreateIndexIfNotExists;

-- 7. Reactivar restricciones
SET FOREIGN_KEY_CHECKS = 1;
