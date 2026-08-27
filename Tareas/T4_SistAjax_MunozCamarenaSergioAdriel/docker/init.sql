CREATE DATABASE IF NOT EXISTS ajax_crud;
USE ajax_crud;

CREATE TABLE IF NOT EXISTS registros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    texto VARCHAR(300) NOT NULL,
    numero INT NOT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT 'default.svg',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO registros (texto, numero, imagen) VALUES
('Laptop Lenovo', 25, 'producto-01.svg'),
('Adaptador HDMI', 12, 'producto-02.svg'),
('Mouse inalámbrico', 37, 'producto-03.svg'),
('Teclado mecánico', 18, 'producto-04.svg'),
('Monitor 24 pulgadas', 8, 'producto-05.svg');

DELIMITER //

CREATE PROCEDURE llenar_registros()
BEGIN
    DECLARE i INT DEFAULT 6;
    WHILE i <= 300 DO
        INSERT INTO registros (texto, numero, imagen)
        VALUES (
            CONCAT('Equipo de prueba ', i),
            FLOOR(1 + RAND() * 100),
            CASE
                WHEN i <= 30 THEN CONCAT('producto-', LPAD(i, 2, '0'), '.svg')
                ELSE 'default.svg'
            END
        );
        SET i = i + 1;
    END WHILE;
END//

DELIMITER ;

CALL llenar_registros();
DROP PROCEDURE llenar_registros;
