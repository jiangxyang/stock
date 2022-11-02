/*
 Navicat Premium Data Transfer

 Source Server Type    : MySQL
 Source Server Version : 5.7.38
 Source Schema         : stock_db

 Target Server Type    : MySQL
 Target Server Version : 5.7.38
 File Encoding         : 65001

 Date: 02/11/2022 11:12:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for iv_stock
-- ----------------------------
DROP TABLE IF EXISTS `iv_stock`;
CREATE TABLE `iv_stock`  (
  `stock_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ts_code` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'TS代码',
  `symbol` char(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '股票代码',
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '股票名称',
  `area` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '地域',
  `industry` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '所属行业',
  `fullname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '股票全称',
  `enname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '英文全称',
  `cnspell` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '拼音缩写',
  `market` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '市场类型',
  `exchange` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '交易所代码',
  `curr_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '交易货币',
  `list_status` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '上市状态 L上市 D退市 P暂停上市',
  `list_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '上市日期',
  `delist_date` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '退市日期',
  `is_hs` char(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '是否沪深港通标的，N否 H沪股通 S深股通',
  `create_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`stock_id`) USING BTREE,
  UNIQUE INDEX `un_symbol`(`symbol`) USING BTREE,
  UNIQUE INDEX `unique_ts_code`(`ts_code`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '股票列表' ROW_FORMAT = Dynamic;
-- ----------------------------
-- Table structure for iv_stock_daily
-- ----------------------------
DROP TABLE IF EXISTS `iv_stock_daily`;
CREATE TABLE `iv_stock_daily`  (
  `stock_day_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '股票列表主键id',
  `ts_code` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'TS股票代码',
  `trade_date` int(11) NOT NULL COMMENT '交易日期',
  `open` decimal(20, 2) NULL DEFAULT NULL COMMENT '开盘价',
  `high` decimal(20, 2) NULL DEFAULT NULL COMMENT '最高价',
  `low` decimal(20, 2) NULL DEFAULT NULL COMMENT '最低价',
  `close` decimal(20, 2) NULL DEFAULT NULL COMMENT '收盘价',
  `pre_close` decimal(20, 2) NULL DEFAULT NULL COMMENT '昨收价',
  `change` decimal(20, 2) NULL DEFAULT NULL COMMENT '涨跌额',
  `pct_chg` decimal(20, 4) NULL DEFAULT NULL COMMENT '涨跌幅 （未复权）',
  `vol` decimal(20, 2) NULL DEFAULT NULL COMMENT '成交量 （手）',
  `amount` decimal(20, 4) NULL DEFAULT NULL COMMENT '成交额 （千元）',
  `create_time` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`stock_day_id`) USING BTREE,
  UNIQUE INDEX `unique_ts_tr`(`ts_code`, `trade_date`) USING BTREE,
  INDEX `normal_stock_id`(`stock_id`) USING BTREE,
  INDEX `normal_trade`(`trade_date`) USING BTREE,
  INDEX `normal_ts_code`(`ts_code`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'A股日线行情表' ROW_FORMAT = Fixed;

SET FOREIGN_KEY_CHECKS = 1;
