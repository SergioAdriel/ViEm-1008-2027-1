CREATE DATABASE IF NOT EXISTS ajax_crud;
USE ajax_crud;

CREATE TABLE IF NOT EXISTS registros (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    texto VARCHAR(150) NOT NULL,
    numero INT NOT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT 'default.svg',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO registros (texto, numero, imagen) VALUES
('Laptop Lenovo', 25, 'default.svg'),
('Adaptador HDMI', 12, 'default.svg'),
('Mouse inalámbrico', 37, 'default.svg'),
('Teclado mecánico', 18, 'default.svg'),
('Monitor 24 pulgadas', 8, 'default.svg');

DELIMITER //

CREATE PROCEDURE llenar_registros()
BEGIN
    DECLARE i INT DEFAULT 6;
    WHILE i <= 250 DO
        INSERT INTO registros (texto, numero, imagen)
        VALUES (
            CONCAT('Equipo de prueba ', i),
            FLOOR(1 + RAND() * 100),
            'default.svg'
        );
        SET i = i + 1;
    END WHILE;
END//

DELIMITER ;

CALL llenar_registros();
DROP PROCEDURE llenar_registros;
