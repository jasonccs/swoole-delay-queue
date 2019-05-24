/*
 Navicat Premium Data Transfer

 Source Server         : remote
 Source Server Type    : MySQL
 Source Server Version : 50725
 Source Host           : 118.24.84.222:3306
 Source Schema         : webim

 Target Server Type    : MySQL
 Target Server Version : 50725
 File Encoding         : 65001

 Date: 30/04/2019 19:42:50
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for sp_order
-- ----------------------------
DROP TABLE IF EXISTS `sp_order`;
CREATE TABLE `sp_order`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(90) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `order_stats` tinyint(1) NOT NULL DEFAULT -1 COMMENT '-1 待支付 0 取消 2 已支付',
  `create_at` int(11) NOT NULL,
  `created_at` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `updated_at` varchar(90) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `orderSn`(`order_sn`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;
