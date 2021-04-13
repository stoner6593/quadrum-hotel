CREATE TABLE al_tipo_peradicional (
  id_tipo int(11) NOT NULL AUTO_INCREMENT,
  tipoperadic_descrip varchar(45) DEFAULT NULL,
  tipoperadic_estado int(11) DEFAULT '1',
  PRIMARY KEY (id_tipo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;