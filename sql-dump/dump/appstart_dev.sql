/*
Navicat MySQL Data Transfer

Source Server         : 192.168.3.42 Server
Source Server Version : 50508
Source Host           : 192.168.3.42:3306
Source Database       : appstart_dev

Target Server Type    : MYSQL
Target Server Version : 50508
File Encoding         : 65001

Date: 2012-09-07 22:03:50
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `business_type`
-- ----------------------------
DROP TABLE IF EXISTS `business_type`;
CREATE TABLE `business_type` (
  `business_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `last_updated_by` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`business_type_id`),
  UNIQUE KEY `unique_name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of business_type
-- ----------------------------
INSERT INTO `business_type` VALUES ('1', 'Real Estate Edited', '1', '2012-08-31 13:47:01', '0', '0000-00-00 00:00:00');
INSERT INTO `business_type` VALUES ('2', 'Tirth Estate', '1', '2012-08-30 14:20:09', '1', '2012-08-30 14:20:15');

-- ----------------------------
-- Table structure for `contact`
-- ----------------------------
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `contact_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `location` varchar(50) DEFAULT NULL,
  `address` varchar(90) DEFAULT NULL,
  `phone_1` varchar(20) DEFAULT NULL,
  `phone_2` varchar(20) DEFAULT NULL,
  `phone_3` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `latitude` varchar(15) DEFAULT NULL,
  `longitude` varchar(15) DEFAULT NULL,
  `email_1` varchar(60) DEFAULT NULL,
  `email_2` varchar(60) DEFAULT NULL,
  `email_3` varchar(60) DEFAULT NULL,
  `website` varchar(60) DEFAULT NULL,
  `timings` varchar(30) DEFAULT NULL,
  `order` tinyint(4) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `logo` varchar(120) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of contact
-- ----------------------------
INSERT INTO `contact` VALUES ('1', null, 'Test Location 1', 'Vadodara', '', '', '', '', '22.3073095', '73.181097599999', '', '', '', '', '', null, null, 'statistics.png', '1', '1970-01-01 00:00:00', '1', '1970-01-01 00:00:00');

-- ----------------------------
-- Table structure for `customer`
-- ----------------------------
DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `app_access_id` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(30) DEFAULT NULL,
  `business_type_id` int(11) DEFAULT NULL,
  `address` varchar(90) DEFAULT NULL,
  `country` varchar(30) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `contact_person_name` varchar(50) DEFAULT NULL,
  `contact_person_email` varchar(60) DEFAULT NULL,
  `contact_person_phone` varchar(20) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `uk_app_access_id` (`app_access_id`) USING BTREE,
  KEY `fk_business_type` (`business_type_id`) USING BTREE,
  KEY `fk_template` (`template_id`) USING BTREE,
  CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `template` (`template_id`),
  CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`business_type_id`) REFERENCES `business_type` (`business_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer
-- ----------------------------
INSERT INTO `customer` VALUES ('58', 'Co-Valuate', '48', 'Tirth Bodawala', '2', '21/B Paragraj Society,\r\nHarni Warasiya Ring Road,\r\nVadodara', 'India', 'Vadodara', 'Kirtan Bodawala', 'kirtanking@gmail.com', '+919727130422', '1', '3', '1', '1970-01-01 01:00:00', '1', '1970-01-01 01:00:00');
INSERT INTO `customer` VALUES ('67', 'Co-Valuate 2', '57', 'Tirth Bodawala', '2', '21/B Paragraj Society,\r\nHarni Warasiya Ring Road,', 'India', 'Vadodara', 'Kirtan Bodawala', 'kirtanking@gmail.com', '+919727130422', '1', '3', '1', '1970-01-01 01:00:00', '1', '1970-01-01 01:00:00');
INSERT INTO `customer` VALUES ('68', 'Co-Valuate 3', '58', 'Tirth Bodawala', '2', '21/B Paragraj Society,\r\nHarni Warasiya Ring Road,', 'India', 'Vadodara', 'Kirtan Bodawala', 'kirtanking@gmail.com', '+919727130422', '1', '3', '1', '1970-01-01 01:00:00', '1', '1970-01-01 01:00:00');
INSERT INTO `customer` VALUES ('69', 'Co-Valuate 4', '59', 'Tirth Bodawala', '2', '21/B Paragraj Society,\r\nHarni Warasiya Ring Road', 'India', 'Vadodara', 'Kirtan Bodawala', 'kirtanking@gmail.com', '+919727130422', '1', '4', '1', '1970-01-01 01:00:00', '1', '1970-01-01 01:00:00');
INSERT INTO `customer` VALUES ('70', 'Co-Valuate 5', '60', 'Tirth Bodawala', '2', '21212', 'India', 'Vadodara', '', 'kirtanking@gmail.com', '+919727130422', '1', '4', '1', '2012-09-07 20:52:51', '1', '1970-01-01 01:00:00');

-- ----------------------------
-- Table structure for `customer_configuration`
-- ----------------------------
DROP TABLE IF EXISTS `customer_configuration`;
CREATE TABLE `customer_configuration` (
  `customer_configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `font_type` varchar(90) DEFAULT NULL,
  `font_color` varchar(15) DEFAULT NULL,
  `font_size` varchar(15) DEFAULT NULL,
  `spacing` varchar(15) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_configuration_id`),
  KEY `fk_customer_config` (`customer_id`) USING BTREE,
  CONSTRAINT `customer_configuration_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_configuration
-- ----------------------------

-- ----------------------------
-- Table structure for `customer_module`
-- ----------------------------
DROP TABLE IF EXISTS `customer_module`;
CREATE TABLE `customer_module` (
  `customer_module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_number` int(11) DEFAULT NULL,
  `visibility` tinyint(4) DEFAULT NULL,
  `screen_name` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `background_image` varchar(256) DEFAULT NULL,
  `share` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`customer_module_id`),
  KEY `fk_customer_id` (`customer_id`) USING BTREE,
  KEY `fk_module_id` (`module_id`) USING BTREE,
  CONSTRAINT `customer_module_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`),
  CONSTRAINT `customer_module_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_module
-- ----------------------------
INSERT INTO `customer_module` VALUES ('1', '2', '70', '1', '0', 'Tirth', '1', null, null);
INSERT INTO `customer_module` VALUES ('2', '4', '70', '2', '0', 'Tirth Bodawala', '1', null, null);
INSERT INTO `customer_module` VALUES ('3', '3', '70', '2', '0', 'Tirth', '0', null, null);

-- ----------------------------
-- Table structure for `customer_payment`
-- ----------------------------
DROP TABLE IF EXISTS `customer_payment`;
CREATE TABLE `customer_payment` (
  `customer_payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `payment_mode` varchar(15) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `invoice_amount` decimal(10,2) DEFAULT NULL,
  `invoice_number` int(11) DEFAULT NULL,
  `comments` varchar(90) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_payment_id`),
  KEY `fk_customer` (`customer_id`) USING BTREE,
  CONSTRAINT `customer_payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_payment
-- ----------------------------

-- ----------------------------
-- Table structure for `customer_sync`
-- ----------------------------
DROP TABLE IF EXISTS `customer_sync`;
CREATE TABLE `customer_sync` (
  `customer_sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `sync_date_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  PRIMARY KEY (`customer_sync_id`),
  KEY `fk_sync_customer` (`customer_id`) USING BTREE,
  CONSTRAINT `customer_sync_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_sync
-- ----------------------------

-- ----------------------------
-- Table structure for `home_wallpaper`
-- ----------------------------
DROP TABLE IF EXISTS `home_wallpaper`;
CREATE TABLE `home_wallpaper` (
  `wallpaper_id` int(11) NOT NULL AUTO_INCREMENT,
  `image_ipad` varchar(200) DEFAULT NULL,
  `image_iphone` varchar(200) DEFAULT NULL,
  `image_ipad_3` varchar(200) DEFAULT NULL,
  `image_android` varchar(200) DEFAULT NULL,
  `link_to_module` varchar(100) NOT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `order` varchar(50) DEFAULT NULL,
  `last_updated_by` varchar(80) NOT NULL DEFAULT '',
  `last_updated_at` datetime NOT NULL,
  `created_by` varchar(80) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`wallpaper_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of home_wallpaper
-- ----------------------------
INSERT INTO `home_wallpaper` VALUES ('70', '', '', '', '', '4', '1', '6', '2', '1970-01-01 00:00:00', '2', '1970-01-01 00:00:00');
INSERT INTO `home_wallpaper` VALUES ('74', '', '', '', '', '4', '0', '2', '2', '1970-01-01 00:00:00', '2', '1970-01-01 00:00:00');
INSERT INTO `home_wallpaper` VALUES ('75', '', '', '', '', '5', '0', '0', '2', '1970-01-01 00:00:00', '2', '1970-01-01 00:00:00');

-- ----------------------------
-- Table structure for `module`
-- ----------------------------
DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `path` varchar(100) NOT NULL,
  `status` smallint(6) NOT NULL,
  `last_updated_by` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of module
-- ----------------------------
INSERT INTO `module` VALUES ('1', 'Tirth', 'Tirth\'s Description', '/home/', '1', '1', '2012-08-29 15:18:46', '1', '2012-08-29 15:18:53');
INSERT INTO `module` VALUES ('2', 'Tirth', 'Tirth\' Desc', '/', '1', '1', '2012-08-29 00:00:00', '1', '2012-08-29 00:00:00');
INSERT INTO `module` VALUES ('3', 'Tirth', 'Tirth\' Desc', '/', '1', '1', '2012-08-29 00:00:00', '1', '2012-08-29 00:00:00');
INSERT INTO `module` VALUES ('4', 'Tirth Bodawala', 'Tirth\' Desc', '/', '1', '1', '2012-08-29 00:00:00', '1', '2012-08-29 00:00:00');
INSERT INTO `module` VALUES ('5', 'Tirth Gopaldas Bodawala', '', '', '1', '1', '2012-09-07 16:48:15', '1', '2012-09-07 16:48:18');

-- ----------------------------
-- Table structure for `system_user`
-- ----------------------------
DROP TABLE IF EXISTS `system_user`;
CREATE TABLE `system_user` (
  `system_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(90) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` smallint(6) NOT NULL,
  `last_updated_by` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`system_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of system_user
-- ----------------------------
INSERT INTO `system_user` VALUES ('1', 'admin@aksystems.com', 'aks@123', '1', '1', '2012-08-30 08:18:48', '1', '2012-08-23 18:57:41');
INSERT INTO `system_user` VALUES ('4', 'ajay@aksystems-inc.com', 'aks@123', '2', '1', '2012-09-06 13:17:27', '1', '2012-08-31 09:30:53');
INSERT INTO `system_user` VALUES ('5', 'manan123@gmail.com', 'aks@123', '2', '1', '1970-01-01 00:00:00', '1', '1970-01-01 00:00:00');

-- ----------------------------
-- Table structure for `template`
-- ----------------------------
DROP TABLE IF EXISTS `template`;
CREATE TABLE `template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `business_type_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `status` smallint(6) NOT NULL,
  `last_updated_by` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`template_id`),
  KEY `IDX_97601F83987F37DE` (`business_type_id`) USING BTREE,
  CONSTRAINT `template_ibfk_1` FOREIGN KEY (`business_type_id`) REFERENCES `business_type` (`business_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of template
-- ----------------------------
INSERT INTO `template` VALUES ('3', '2', 'Tirth', '1', '1', '2012-08-30 17:14:31', '1', '2012-08-27 17:14:36');
INSERT INTO `template` VALUES ('4', '2', 'New Temoplate', '1', '1', '2012-09-07 16:28:21', '1', '2012-09-07 16:28:27');

-- ----------------------------
-- Table structure for `template_module`
-- ----------------------------
DROP TABLE IF EXISTS `template_module`;
CREATE TABLE `template_module` (
  `template_module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `template_id` int(11) DEFAULT NULL,
  `status` smallint(6) NOT NULL,
  `last_updated_by` int(11) NOT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`template_module_id`),
  KEY `IDX_7CC2FDAAFC2B591` (`module_id`) USING BTREE,
  KEY `IDX_7CC2FDA5DA0FB8` (`template_id`) USING BTREE,
  CONSTRAINT `template_module_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `template` (`template_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `template_module_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of template_module
-- ----------------------------
INSERT INTO `template_module` VALUES ('1', '2', '3', '1', '1', '2012-09-05 17:17:30', '1', '2012-09-05 17:17:34');
INSERT INTO `template_module` VALUES ('2', '3', '3', '1', '1', '2012-09-05 17:17:50', '1', '2012-09-05 17:17:58');
INSERT INTO `template_module` VALUES ('3', '4', '3', '0', '1', '2012-09-05 17:18:22', '1', '2012-09-05 17:18:25');
INSERT INTO `template_module` VALUES ('4', '2', '4', '1', '1', '2012-09-07 16:43:34', '1', '2012-09-07 16:45:02');
INSERT INTO `template_module` VALUES ('5', '4', '4', '1', '1', '2012-09-07 16:45:16', '1', '2012-09-07 16:45:19');

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(90) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_username` (`username`) USING BTREE,
  KEY `fk_group` (`user_group_id`) USING BTREE,
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('59', 'tirthbodawala4', 'king123#', 'Tirth Bodawala', '+917600540105', 'tirthbodawala@yahoo.co.in', '1', '61', '59', '2012-09-07 18:25:45', '59', '2012-09-04 18:25:38');
INSERT INTO `user` VALUES ('60', 'tirthbodawala5', 'tirth', 'Tirth Bodawala', '+917600540105', 'tirthbodawala@yahoo.co.in', '1', '62', '60', '2012-09-07 20:52:51', '60', '2012-09-06 18:25:32');

-- ----------------------------
-- Table structure for `user_group`
-- ----------------------------
DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_group_id`),
  UNIQUE KEY `uk_customer_user_group` (`customer_id`,`name`) USING BTREE,
  KEY `fk_user_group` (`customer_id`) USING BTREE,
  CONSTRAINT `user_group_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_group
-- ----------------------------
INSERT INTO `user_group` VALUES ('61', '69', 'Administrator', '59', '1970-01-01 01:00:00', '59', '1970-01-01 01:00:00');
INSERT INTO `user_group` VALUES ('62', '70', 'Administrator', '60', '1970-01-01 01:00:00', '60', '1970-01-01 01:00:00');

-- ----------------------------
-- Table structure for `user_group_module`
-- ----------------------------
DROP TABLE IF EXISTS `user_group_module`;
CREATE TABLE `user_group_module` (
  `user_group_module_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `last_updated_by` int(11) DEFAULT NULL,
  `last_updated_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_group_module_id`),
  KEY `fk_group_module_group` (`user_group_id`) USING BTREE,
  KEY `fk_group_module` (`module_id`) USING BTREE,
  CONSTRAINT `user_group_module_ibfk_1` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`),
  CONSTRAINT `user_group_module_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of user_group_module
-- ----------------------------
INSERT INTO `user_group_module` VALUES ('22', '62', '4', '1', '60', '2012-09-07 20:52:51', '60', '1970-01-01 01:00:00');
INSERT INTO `user_group_module` VALUES ('23', '62', '3', '0', '60', '2012-09-07 20:52:39', '60', '1970-01-01 01:00:00');
INSERT INTO `user_group_module` VALUES ('24', '62', '2', '1', '60', '2012-09-07 20:52:51', '60', '1970-01-01 01:00:00');
