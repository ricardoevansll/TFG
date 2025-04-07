-- Nuevos campos para registrar otros datos en tabla consumo (uso cpu, consumo energético y renovable)
ALTER TABLE consumo 
ADD COLUMN cpu_usage FLOAT NOT NULL AFTER carbon, 
ADD COLUMN power_cons FLOAT NOT NULL AFTER cpu_usage, 
ADD COLUMN renovable TINYINT(1) NOT NULL DEFAULT 0 AFTER power_cons;


-- Nueva tabla para gestión el reinicio de clave de los usuarios
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    INDEX(email),
    INDEX(token)
);



