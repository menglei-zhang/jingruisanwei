SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `snake_order`;
CREATE TABLE `snake_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pc_src` varchar(5000) DEFAULT '' COMMENT '文件组',
  `username` varchar(50) NOT NULL COMMENT '客户名称',
  `addtime` datetime NOT NULL,
  `place` varchar(255) DEFAULT NULL,
  `order_sn` varchar(255) NOT NULL COMMENT '订单号',
  `workname` varchar(255) NOT NULL COMMENT '业务员名称',
  `uptime` char(10) NOT NULL COMMENT '交货日期',
  `craft` varchar(255) NOT NULL COMMENT '制作工艺',
  `sanding` varchar(255) NOT NULL COMMENT '打磨要求',
  `file_name` varchar(255) DEFAULT NULL COMMENT '文件名称',
  `num` int(11) NOT NULL COMMENT '数量',
  `weight` varchar(255) NOT NULL COMMENT '克数',
  `material` varchar(255) NOT NULL COMMENT '打印材料',
  `be_careful` text CHARACTER SET utf8mb4 COMMENT '注意事项',
  `confirm` varchar(255) NOT NULL COMMENT '确认生产部',
  `programmer` varchar(255) DEFAULT NULL COMMENT '编程人员',
  `machine_number` varchar(255) DEFAULT NULL COMMENT '上机机号',
  `date` datetime DEFAULT NULL COMMENT '下机时间',
  `static` int(11) DEFAULT '1' COMMENT '订单状态    ',
  `debuff` int(255) NOT NULL DEFAULT '1' COMMENT '异常状态',
  `user_id` int(11) DEFAULT NULL,
  `update` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`username`),
  KEY `order_sn` (`order_sn`) USING BTREE,
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2335 DEFAULT CHARSET=utf8;

insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2326','/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png','卡莱时尚有限公司','2019-05-07 12:17:41','222','123321123452131','小王','上午','SLA','C','','5','5','白色材料','111111','3D打印','陈江','12131231231231','2019-05-05 16:01:40','5','2','2','2019-04-28');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2327','/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20190428/567cbcf4cc547ca2a2f2159ee0e821f3.txt','上海科电科技有限公司','2019-05-07 16:57:44','222','1233211234565','哓羽','上午','SLA','C','','5','5','白色材料','111111111','CNC','陈江','12131231231231','2019-05-05 18:20:34','2','2','6','2019-04-28');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2328','/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20190428/567cbcf4cc547ca2a2f2159ee0e821f3.txt','上海科电科技有限公司','2019-05-05 16:58:11','222','123321123453','凯凯','下午','SLA','C','','5','5','白色材料','111111','3D打印','陈江','12131231231231','2019-05-05 18:20:23','2','1','7','2019-05-15');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2329','/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png','卡莱时尚有限公司','2019-04-26 16:59:36','222','123321123451','小元','下午','SLA','C','','5','5','白色材料','11111111','3D打印','陈江','12131231231231','2019-05-05 18:20:10','2','2','3','2019-04-28');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2330','/public/upload/20170916/1e915c70dbb9d3e8a07bede7b64e4cff.png,/public/upload/20190428/567cbcf4cc547ca2a2f2159ee0e821f3.txt','卡莱时尚有限公司','2019-04-26 16:57:44','333','123321123452','哓羽','上午','SLA','C','','5','5','白色材料','111111111','3D打印','陈江','12131231231231','2019-04-29 11:47:01','2','2','6','2019-04-28');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2331','','卡莱时尚有限公司','2019-05-09 12:18:07','333','123321123456','哓羽','上午','SLA','C','','5','5','白色材料','123123123','3D打印','','','','2','2','6','2019-05-09');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2332','','卡莱时尚有限公司','2019-05-09 12:20:27','333','123321123456','哓羽','上午','SLA','C','','5','5','白色材料','123123123123','3D打印','','','','1','2','3','2019-05-09');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2333','','卡莱时尚有限公司','2019-05-09 13:30:17','333','123321123456','凯凯','上午','SLA','C','','5','5','白色材料','1231231231','3D打印','','','','1','2','7','2019-05-09');
insert into `snake_order`(`id`,`pc_src`,`username`,`addtime`,`place`,`order_sn`,`workname`,`uptime`,`craft`,`sanding`,`file_name`,`num`,`weight`,`material`,`be_careful`,`confirm`,`programmer`,`machine_number`,`date`,`static`,`debuff`,`user_id`,`update`) values('2334','','卡莱时尚有限公司','2019-05-09 13:31:41','333','123321123456','凯凯','下午','SLA','C','','5','5','白色材料','123123123','3D打印','','','','1','2','7','2019-05-09');
