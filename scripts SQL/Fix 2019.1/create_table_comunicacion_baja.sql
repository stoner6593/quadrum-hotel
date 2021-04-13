CREATE TABLE `comunicacion_baja` (
  `id_cb` int(11) NOT NULL AUTO_INCREMENT,
  `correlativo` int(11) DEFAULT NULL,
  `cod_estado_envio_sunat` varchar(10) DEFAULT NULL,
  `mensaje_envio_sunat` varchar(500) DEFAULT NULL,
  `nombre_archivo_zip` varchar(100) DEFAULT NULL,
  `nombre_archivo` varchar(100) DEFAULT NULL,
  `nombre_documento` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cb`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
