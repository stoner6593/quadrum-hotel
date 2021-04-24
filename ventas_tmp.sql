/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : quadrum

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-04-23 23:06:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ventas_tmp
-- ----------------------------
DROP TABLE IF EXISTS `ventas_tmp`;
CREATE TABLE `ventas_tmp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `idproducto` int(10) NOT NULL,
  `nombre` varchar(250) NOT NULL,
  `cantidad` int(10) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `idalquiler` int(10) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=27345 DEFAULT CHARSET=utf8;
SET FOREIGN_KEY_CHECKS=1;
