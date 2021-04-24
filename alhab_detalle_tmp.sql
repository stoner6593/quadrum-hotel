/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : quadrum

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-04-23 23:06:52
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for alhab_detalle_tmp
-- ----------------------------
DROP TABLE IF EXISTS `alhab_detalle_tmp`;
CREATE TABLE `alhab_detalle_tmp` (
  `idtmp` int(10) NOT NULL AUTO_INCREMENT,
  `tipoalquiler` int(1) NOT NULL,
  `fechadesde` datetime DEFAULT NULL,
  `fechahasta` datetime DEFAULT NULL,
  `nrohoras` int(10) DEFAULT NULL,
  `nrodias` int(10) DEFAULT NULL,
  `costohora` decimal(10,2) DEFAULT NULL,
  `costodia` decimal(10,2) DEFAULT NULL,
  `formapago` int(1) DEFAULT NULL,
  `totalefectivo` decimal(10,2) DEFAULT NULL,
  `totalvisa` decimal(10,2) DEFAULT NULL,
  `totalmastercard` decimal(10,2) DEFAULT NULL,
  `estadopago` int(1) DEFAULT NULL,
  `costoingresoanticipado` decimal(10,2) DEFAULT NULL,
  `horaadicional` int(10) DEFAULT NULL,
  `costohoraadicional` decimal(10,2) DEFAULT NULL,
  `huespedadicional` int(10) DEFAULT NULL,
  `costohuespedadicional` decimal(10,2) DEFAULT NULL,
  `preciounitario` decimal(10,2) DEFAULT NULL,
  `cantidad` int(10) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `comentarios` text,
  `tiporeserva` int(11) DEFAULT '0',
  `idusuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`idtmp`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;
