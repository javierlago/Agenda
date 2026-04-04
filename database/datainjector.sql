-- 1. CONFIGURACIÓN INICIAL
SET @mi_user_id = 1;  -- <--- Cambia el ID del dueño de los contactos aquí
SET @cantidad = 50;   -- <--- Cambia cuántos quieres generar aquí

-- 2. BORRAMOS EL PROCEDIMIENTO SI YA EXISTÍA (Para evitar bloqueos)
DROP PROCEDURE IF EXISTS InjectarContactos;

-- 3. CREAMOS EL PROCEDIMIENTO DE NUEVO
DELIMITER //

CREATE PROCEDURE InjectarContactos(IN target_user_id INT, IN num_registros INT)
BEGIN
    DECLARE i INT DEFAULT 1;
    
    WHILE i <= num_registros DO
        INSERT INTO contacts (user_id, name, phone, email, description, created_at)
        VALUES (
            target_user_id, 
            CONCAT('Contacto Pro ', i), 
            CONCAT('6', LPAD(FLOOR(RAND() * 89999999 + 10000000), 8, '0')), -- Teléfono aleatorio realista
            CONCAT('user', i, '_test@ejemplo.com'),
            CONCAT('Descripción de prueba para el contacto número ', i, '. Generado automáticamente.'),
            DATE_SUB(NOW(), INTERVAL i HOUR) -- Fechas escalonadas para probar el ORDER BY
        );
        SET i = i + 1;
    END WHILE;
END //

DELIMITER ;

-- 4. EJECUCIÓN
-- Aquí es donde le pasas los parámetros. 
-- Puedes cambiar @mi_user_id por un número directo si prefieres.
CALL InjectarContactos(@mi_user_id, @cantidad);

-- 5. VERIFICACIÓN FINAL
SELECT id, user_id, name, phone, created_at 
FROM contacts 
WHERE user_id = @mi_user_id 
ORDER BY id DESC 
LIMIT 10;