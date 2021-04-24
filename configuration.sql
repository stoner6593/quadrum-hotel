/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50724
Source Host           : localhost:3306
Source Database       : quadrum

Target Server Type    : MYSQL
Target Server Version : 50724
File Encoding         : 65001

Date: 2021-04-23 23:07:15
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for configuration
-- ----------------------------
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE `configuration` (
  `config_name` varchar(30) NOT NULL,
  `config_value` varchar(75) NOT NULL,
  `config_value_1` varchar(100) DEFAULT NULL,
  `config_value_2` varchar(100) DEFAULT NULL,
  `config_value_3` varchar(100) DEFAULT NULL,
  `config_value_4` varchar(100) DEFAULT NULL,
  `config_value_5` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`config_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET FOREIGN_KEY_CHECKS=1;
