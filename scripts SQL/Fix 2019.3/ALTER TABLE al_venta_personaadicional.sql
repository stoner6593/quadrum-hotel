ALTER TABLE al_venta_personaadicional 
ADD COLUMN id_tipo INT NULL AFTER nacimiento;
ALTER TABLE al_venta_personaadicional 
ADD COLUMN fecha_registro DATETIME NULL DEFAULT CURRENT_TIMESTAMP AFTER id_tipo;
