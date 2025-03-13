-- Incluyendo campo en la tabla usuarios para registar la aceptación de terminos y condiciones 11/03/2025
ALTER TABLE usuario ADD COLUMN terms_accepted TINYINT(1) DEFAULT 0;
