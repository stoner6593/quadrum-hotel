ALTER TABLE hab_venta 
ADD COLUMN preciobooking DECIMAL(10,2) NULL DEFAULT 0 AFTER precio12vs,
ADD COLUMN precioreservaweb DECIMAL(10,2) NULL DEFAULT 0 AFTER preciobooking;
