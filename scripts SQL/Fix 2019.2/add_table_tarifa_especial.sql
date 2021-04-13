CREATE TABLE tarifa_especial (
  id_tarifa int(11) NOT NULL AUTO_INCREMENT,
  descripcion_tarifa varchar(150) DEFAULT NULL,
  fecha_tarifa date DEFAULT NULL,
  idtipo int(11) DEFAULT NULL,
  precio_dia decimal(10,2) DEFAULT NULL,
  precio_hora_1 decimal(10,2) DEFAULT NULL,
  precio_hora_2 decimal(10,2) DEFAULT NULL,
  precio_hora_adicional decimal(10,2) DEFAULT NULL,
  precio_huesped_adicional decimal(10,2) DEFAULT NULL,
  estado_tarifa int(11) DEFAULT '1',
  fecha_registro timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_tarifa)
);
