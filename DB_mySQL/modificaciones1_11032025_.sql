-- Incluyendo campo en la tabla recurso para el cálculo de pue y co2 con programa python (exporter.py) 02/03/2025 
ALTER TABLE recurso ADD COLUMN energia_renovable TINYINT(1) DEFAULT 0; 
