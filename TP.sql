/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50540
Source Host           : localhost:3306
Source Database       : TP

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2018-10-04 15:07:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `article`
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `time` varchar(20) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of article
-- ----------------------------

-- ----------------------------
-- Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `abridge` varchar(30) DEFAULT NULL COMMENT '简称',
  `sort` int(3) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES ('1', '爱奇艺会员', 'http://oz3suzzsp.bkt.clouddn.com/QQ%E5%9B%BE%E7%89%8720171108021205.jpg', 'iqiyi', '0');
INSERT INTO `categories` VALUES ('2', '优酷会员', 'http://oz3suzzsp.bkt.clouddn.com/%E4%BC%98%E9%85%B7.jpg', 'youku', '1');
INSERT INTO `categories` VALUES ('3', '芒果TV会员', 'http://oz3suzzsp.bkt.clouddn.com/%E8%8A%92%E6%9E%9C%E6%BF%80%E6%B4%BB%E7%A0%81.jpg', 'mgtv', '1');
INSERT INTO `categories` VALUES ('8', '多多币充值', 'http://file.setotoo.cn/sdnewkmi.png', 'sdnews', '0');
INSERT INTO `categories` VALUES ('14', '乐视会员', 'http://oz3suzzsp.bkt.clouddn.com/%E4%B9%90%E8%A7%86%E6%BF%80%E6%B4%BB%E7%A0%81.jpg', 'letv', '0');

-- ----------------------------
-- Table structure for `daili`
-- ----------------------------
DROP TABLE IF EXISTS `daili`;
CREATE TABLE `daili` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL COMMENT '代理用户名',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `email` varchar(50) DEFAULT NULL,
  `money` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `level` int(1) NOT NULL DEFAULT '1' COMMENT '代理商等级',
  `create_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of daili
-- ----------------------------

-- ----------------------------
-- Table structure for `discount`
-- ----------------------------
DROP TABLE IF EXISTS `discount`;
CREATE TABLE `discount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quanma` varchar(20) NOT NULL,
  `status` int(1) NOT NULL,
  `ddid` varchar(25) NOT NULL,
  `discount` decimal(3,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of discount
-- ----------------------------

-- ----------------------------
-- Table structure for `goods`
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `images` varchar(255) NOT NULL DEFAULT '__STATIC__/images/noimg.jpg',
  `abridge` varchar(30) DEFAULT NULL,
  `price` decimal(6,2) NOT NULL DEFAULT '0.10',
  `Introduction` text,
  `sort` int(3) NOT NULL DEFAULT '0',
  `dailiprice` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '代理拿货价',
  `sales` int(11) NOT NULL DEFAULT '0' COMMENT '销量',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '商品状态 0出售中 1已下架',
  `mansl` int(11) NOT NULL,
  `yhprice` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods
-- ----------------------------
INSERT INTO `goods` VALUES ('1', '爱奇艺会员一个月【测试】', 'http://app.3987.com/uploadfile/2016/0225/20160225012015396.jpg', 'iqiyi', '0.01', '<p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p></p><p><span style=\"background-color: rgb(255, 255, 255);\"><b>测试上传图片，</b></span><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍，</b><b>此处是商品介绍</b></p><p><b>此处是商品介绍</b> </p><p><b>此处是商品介绍</b></p><p><b>此处是商品介绍</b></p><p><b>此处是商品介绍</b> <span style=\"background-color: rgb(255, 255, 255);\"><b><br></b></span></p><p></p><p><br></p><p></p><p><br></p><p></p><p><br></p>', '0', '0.01', '177', '0', '0', '0.00');
INSERT INTO `goods` VALUES ('2', '优酷会员一个月【测试】', 'http://pic.coolchuan.com/anzhuoyuan/download/coolchuan/e9a1b027ecf82e4dee5086aaa7d93159.jpg', 'youku', '0.02', '本商品为测试商品，仅用于程序测试，所获得的卡密并非是真的优酷会员卡密', '0', '0.01', '18', '0', '0', '0.00');
INSERT INTO `goods` VALUES ('3', '芒果tv会员永久【测试】', 'http://down.hdpfans.com/attachment/tv/logo/4/201508111439281108.jpg', 'mgtv', '0.03', '本商品为测试商品，仅用于程序测试，所获得的卡密并非是真的芒果tv会员卡密', '0', '0.02', '0', '0', '0', '0.00');
INSERT INTO `goods` VALUES ('31', '乐视会员一个月测试', 'http://buy.lanliulian.com/letv.jpg', 'letv', '0.03', '<p>为商品写点什么吧~11111111</p><p><br></p>', '1', '0.02', '10', '0', '0', '0.00');

-- ----------------------------
-- Table structure for `kami`
-- ----------------------------
DROP TABLE IF EXISTS `kami`;
CREATE TABLE `kami` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goodid` int(5) DEFAULT NULL,
  `kahao` varchar(255) DEFAULT NULL,
  `mima` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `ddid` varchar(50) DEFAULT NULL,
  `time` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=455 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of kami
-- ----------------------------
INSERT INTO `kami` VALUES ('450', '1', null, '111', '0', null, null);
INSERT INTO `kami` VALUES ('451', '1', null, '222', '0', null, null);
INSERT INTO `kami` VALUES ('452', '1', null, '333', '0', null, null);
INSERT INTO `kami` VALUES ('453', '2', null, '2313', '0', null, null);
INSERT INTO `kami` VALUES ('454', '2', null, '123213', '0', null, null);

-- ----------------------------
-- Table structure for `links`
-- ----------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(20) DEFAULT NULL,
  `siteurl` varchar(50) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of links
-- ----------------------------
INSERT INTO `links` VALUES ('5', '忆天云支付', 'https://www.yi66.cn/', '0');
INSERT INTO `links` VALUES ('3', '忆天自动发卡网', 'http://code.fjd100.com/', '1');

-- ----------------------------
-- Table structure for `options`
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sitename` varchar(8) DEFAULT NULL COMMENT '网站名称',
  `siteurl` varchar(50) DEFAULT NULL COMMENT '网站地址',
  `qq` varchar(20) DEFAULT NULL COMMENT '站长QQ',
  `gonggao` text COMMENT '网站首页公告',
  `keywords` varchar(100) DEFAULT NULL COMMENT '网站关键字',
  `description` varchar(200) DEFAULT NULL COMMENT '网站描述',
  `adminuser` varchar(20) DEFAULT NULL COMMENT '后台用户名',
  `adminpass` varchar(50) DEFAULT NULL COMMENT '后台密码',
  `tongji` text COMMENT '统计代码',
  `logoimg` varchar(255) DEFAULT NULL COMMENT 'LOGO图片地址',
  `paytype` int(1) NOT NULL DEFAULT '0' COMMENT '支付方式 0为易支付 1为码支付',
  `partner` int(5) DEFAULT NULL COMMENT '支付商户ID',
  `key` varchar(32) DEFAULT NULL COMMENT '支付密匙',
  `alipay` int(1) DEFAULT '1' COMMENT '支付宝支付 1开启  0不开启',
  `wxpay` int(1) NOT NULL DEFAULT '1' COMMENT '微信支付 1开启 0不开启',
  `qqpay` int(1) NOT NULL DEFAULT '1' COMMENT 'QQ支付 1开启 0不开启',
  `mailon` int(1) NOT NULL DEFAULT '0' COMMENT '是否开启邮箱自动发卡 1开启 0不开启',
  `emailhost` varchar(50) DEFAULT NULL COMMENT '邮箱服务器地址',
  `emailport` int(4) DEFAULT NULL COMMENT '邮箱端口',
  `emailuser` varchar(50) DEFAULT NULL COMMENT '邮箱用户名',
  `emailpass` varchar(50) DEFAULT NULL COMMENT '邮箱密码',
  `mzfid` int(6) DEFAULT NULL COMMENT '码支付ID',
  `mzfkey` varchar(32) DEFAULT NULL COMMENT '码支付通信密匙',
  `mzftoken` varchar(32) DEFAULT NULL COMMENT '码支付token',
  `goodgg` text COMMENT '商品页公告',
  `indexmode` int(1) NOT NULL DEFAULT '0' COMMENT '首页显示模式 0为分类模式 1列表模式',
  `opdl` decimal(6,2) NOT NULL DEFAULT '0.01' COMMENT '代理开通费用',
  `payapi` varchar(50) DEFAULT NULL COMMENT '易支付API',
  `maxsl` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='网站设置';

-- ----------------------------
-- Records of options
-- ----------------------------
INSERT INTO `options` VALUES ('1', '忆天发卡网', 'http://www.22.com/', '715457186', '<p style=\"background-color:#252A40;\">\r\n	<span style=\"color:#FFFFFF;font-size:16px;\"><strong>公告：下单步骤为：选择商品-选择付款方式-付款(本站24小时自动发货)</strong></span><br />\r\n<span><b><span style=\"color:#E53333;\"><span style=\"font-size:16px;color:#E53333;\">微信支付、支付宝支付、QQ钱包支付全部支持，付款页面显示多少就转账多少（不能多也不能少），转账后系统会自动发货，若有问题请联系网站客服！</span></span><br />\r\n<span style=\"font-size:18px;color:#FF9900;\">网站网址更换为www.yi66.cn</span><br />\r\n</b></span> \r\n</p>\r\n<p>\r\n	<img src=\"http://file.setotoo.cn/20180103.png\" style=\"width:100%;\" class=\"w-e-selected\" /> \r\n</p>', '忆天自动发卡,个人自动发卡,免签约支付接口,24小时自动发货', '忆天自动发卡网是一款个人版全新的一款发卡网，支持微信、支付宝、QQ钱包支付,支持7X24无人值守自动发货', 'admin', 'c3284d0f94606de1fd2af172aba15bf3', '<script type=\"text/javascript\">var cnzz_protocol = ((\"https:\" == document.location.protocol) ? \" https://\" : \" http://\");document.write(unescape(\"%3Cspan id=\'cnzz_stat_icon_1267384953\'%3E%3C/span%3E%3Cscript src=\'\" + cnzz_protocol + \"s13.cnzz.com/z_stat.php%3Fid%3D1267384953%26online%3D1%26show%3Dline\' type=\'text/javascript\'%3E%3C/script%3E\"));</script>', '/static/images/logo.png', '0', '123', '13245789789', '1', '0', '1', '0', 'smtp.163.com', '465', '123@163.com', 'l1212', '1233', 'd3q6SkW0xcrXV55662EJkZ1852wpmhAQ', '4fvXFhkkx0NXTFgnJ34565lWiuDRqBY8', '<div style=\"background-color:#252A40;padding:10px;color:yellow;\">\r\n	<span style=\"font-size:16px;\"><b><span style=\"color:yellow;font-size:16px;\"><strong><b><span style=\"color:#FFE500;\">微信支付、支付宝支付、QQ钱包支付全部支持，付款页面显示多少就转账多少（不能多也不能少），转账后系统会自动发货，若有问题请联系网站客服！<br />\r\n</span></b></strong></span></b></span> \r\n	<p>\r\n		<span style=\"font-size:16px;\"><b><span style=\"color:yellow;font-size:16px;\"><strong><b><span style=\"color:#FFE500;\"><b><span style=\"color:#FFE500;\"><span style=\"color:#E53333;font-size:24px;\"><b><span style=\"font-size:18px;color:#FF9900;\">网站网址更换为www.yi66.cn</span></b></span></span></b><br />\r\n</span></b></strong></span></b></span> \r\n	</p>\r\n<span style=\"font-size:16px;\"><b><span><span style=\"font-size:16px;color:#009900;\"><b></b></span></span></b></span> \r\n</div>', '2', '0.40', 'http://pay.yi66.cn/', '0');

-- ----------------------------
-- Table structure for `order`
-- ----------------------------
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ddid` varchar(50) DEFAULT NULL,
  `goodid` int(5) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `price` decimal(6,2) DEFAULT NULL,
  `count` int(2) NOT NULL DEFAULT '1' COMMENT '购买数量',
  `email` varchar(50) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `time` varchar(30) DEFAULT NULL,
  `yeskm` int(1) NOT NULL DEFAULT '0' COMMENT '卡密是否写入数据库',
  `daili` int(5) DEFAULT NULL,
  `payno` varchar(60) DEFAULT NULL COMMENT '流水号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=917 DEFAULT CHARSET=utf8 COMMENT='订单列表';

-- ----------------------------
-- Records of order
-- ----------------------------

-- ----------------------------
-- Table structure for `recharge`
-- ----------------------------
DROP TABLE IF EXISTS `recharge`;
CREATE TABLE `recharge` (
  `id` varchar(30) NOT NULL DEFAULT '',
  `dailiid` int(11) DEFAULT NULL COMMENT '代理ID',
  `money` decimal(7,2) DEFAULT NULL COMMENT '充值金额',
  `type` varchar(50) DEFAULT NULL COMMENT '支付方式',
  `time` varchar(30) DEFAULT NULL COMMENT '充值时间',
  `status` int(1) DEFAULT NULL COMMENT '0失败，1成功',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of recharge
-- ----------------------------
