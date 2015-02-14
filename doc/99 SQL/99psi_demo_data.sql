SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


TRUNCATE TABLE `t_biz_log`;
INSERT INTO `t_biz_log` (`id`, `date_created`, `info`, `ip`, `user_id`, `log_category`) VALUES
(1, '2015-02-14 09:02:12', '登录系统', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(2, '2015-02-14 09:02:17', '进入模块：用户管理', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(3, '2015-02-14 09:21:49', '登录系统', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(4, '2015-02-14 09:21:53', '进入模块：用户管理', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(5, '2015-02-14 09:22:21', '进入模块：权限管理', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(6, '2015-02-14 09:26:43', '进入模块：用户管理', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统'),
(7, '2015-02-14 09:28:46', '进入模块：权限管理', '0.0.0.0', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '系统');

TRUNCATE TABLE `t_config`;
INSERT INTO `t_config` (`id`, `name`, `value`, `note`) VALUES
('2002-01', '销售出库单允许编辑销售单价', '0', '当允许编辑的时候，还需要给用户赋予权限[销售出库单允许编辑销售单价]');

TRUNCATE TABLE `t_customer`;
TRUNCATE TABLE `t_customer_category`;
TRUNCATE TABLE `t_fid`;
INSERT INTO `t_fid` (`fid`, `name`) VALUES
('-9999', '重新登录'),
('-9997', '首页'),
('-9996', '修改我的密码'),
('-8999', '用户管理'),
('-8997', '业务日志'),
('-8996', '权限管理'),
('1001', '商品'),
('1002', '商品计量单位'),
('1003', '仓库'),
('1004', '供应商档案'),
('1007', '客户资料'),
('2000', '库存建账'),
('2001', '采购入库'),
('2002', '销售出库'),
('2002-01', '销售出库'),
('2003', '库存账查询'),
('2004', '应收账款管理'),
('2005', '应付账款管理'),
('2006', '销售退货入库'),
('2007', '采购退货出库'),
('2008', '业务设置');

TRUNCATE TABLE `t_goods`;
TRUNCATE TABLE `t_goods_category`;
TRUNCATE TABLE `t_goods_unit`;
TRUNCATE TABLE `t_invertory`;
TRUNCATE TABLE `t_invertory_detail`;
TRUNCATE TABLE `t_menu_item`;
INSERT INTO `t_menu_item` (`id`, `caption`, `fid`, `parent_id`, `show_order`) VALUES
('01', '文件', NULL, NULL, 1),
('0101', '首页', '-9997', '01', 1),
('0102', '重新登录', '-9999', '01', 2),
('0103', '修改我的密码', '-9996', '01', 3),
('02', '采购', NULL, NULL, 2),
('0201', '采购入库', '2001', '02', 1),
('0202', '采购退货出库', '2007', '02', 2),
('03', '库存', NULL, NULL, 3),
('0301', '库存账查询', '2003', '03', 1),
('0302', '库存建账', '2000', '03', 3),
('04', '销售', NULL, NULL, 4),
('0401', '销售出库', '2002', '04', 1),
('0402', '销售退货入库', '2006', '04', 2),
('05', '客户关系', NULL, NULL, 5),
('0501', '客户资料', '1007', '05', 1),
('06', '资金', NULL, NULL, 6),
('0601', '应收账款管理', '2004', '06', 1),
('0602', '应付账款管理', '2005', '06', 2),
('08', '基础数据', NULL, NULL, 8),
('0801', '商品', '1001', '08', 1),
('0802', '商品计量单位', '1002', '08', 2),
('0803', '仓库', '1003', '08', 3),
('0804', '供应商档案', '1004', '08', 4),
('09', '系统管理', NULL, NULL, 9),
('0901', '用户管理', '-8999', '09', 1),
('0902', '权限管理', '-8996', '09', 2),
('0903', '业务日志', '-8997', '09', 3),
('0904', '业务设置', '2008', '09', 4);

TRUNCATE TABLE `t_org`;
INSERT INTO `t_org` (`id`, `full_name`, `name`, `org_code`, `parent_id`) VALUES
('281F55E0-B3E6-11E4-AC63-782BCBD7746B', '大连安世商贸有限公司\\仓储', '仓储', '0104', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B'),
('2D496F44-B3E5-11E4-AC63-782BCBD7746B', '大连安世商贸有限公司\\采购部', '采购部', '0102', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B'),
('37500493-B3E5-11E4-AC63-782BCBD7746B', '大连安世商贸有限公司\\销售部', '销售部', '0103', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B'),
('4D74E1E4-A129-11E4-9B6A-782BCBD7746B', '大连安世商贸有限公司', '大连安世商贸有限公司', '01', NULL),
('527369D1-B3E5-11E4-AC63-782BCBD7746B', '大连安世商贸有限公司\\总经理办公室', '总经理办公室', '0101', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B'),
('5EBDBE11-A129-11E4-9B6A-782BCBD7746B', '大连安世商贸有限公司\\信息部', '信息部', '0199', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B'),
('92FAB90F-B3E8-11E4-A1A5-782BCBD7746B', '大连安世商贸有限公司\\财务部', '财务部', '0105', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B');

TRUNCATE TABLE `t_payables`;
TRUNCATE TABLE `t_payables_detail`;
TRUNCATE TABLE `t_payment`;
TRUNCATE TABLE `t_permission`;
INSERT INTO `t_permission` (`id`, `fid`, `name`, `note`) VALUES
('-8996', '-8996', '权限管理', '权限管理'),
('-8997', '-8997', '业务日志', '业务日志'),
('-8999', '-8999', '用户管理', '用户管理'),
('1001', '1001', '商品', '商品'),
('1002', '1002', '商品计量单位', '商品计量单位'),
('1003', '1003', '仓库', '仓库'),
('1004', '1004', '供应商档案', '供应商档案'),
('1007', '1007', '客户资料', '客户资料'),
('2000', '2000', '库存建账', '库存建账'),
('2001', '2001', '采购入库', '采购入库'),
('2002', '2002', '销售出库', '销售出库'),
('2002-01', '2002-01', '销售出库单允许编辑销售单价', '销售出库单允许编辑销售单价'),
('2003', '2003', '库存账查询', '库存账查询'),
('2004', '2004', '应收账款管理', '应收账款管理'),
('2005', '2005', '应付账款管理', '应付账款管理'),
('2006', '2006', '销售退货入库', '销售退货入库'),
('2008', '2008', '业务设置', '业务设置');

TRUNCATE TABLE `t_pw_bill`;
TRUNCATE TABLE `t_pw_bill_detail`;
TRUNCATE TABLE `t_receivables`;
TRUNCATE TABLE `t_receivables_detail`;
TRUNCATE TABLE `t_receiving`;
TRUNCATE TABLE `t_recent_fid`;
INSERT INTO `t_recent_fid` (`fid`, `user_id`, `click_count`) VALUES
('-8999', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', 3),
('-8996', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B', 2);

TRUNCATE TABLE `t_role`;
INSERT INTO `t_role` (`id`, `name`) VALUES
('13A411EE-B3E8-11E4-A1A5-782BCBD7746B', '采购经理'),
('38C5705B-B3E8-11E4-A1A5-782BCBD7746B', '销售经理'),
('85E8B5C2-B3E8-11E4-A1A5-782BCBD7746B', '仓储经理'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '系统管理'),
('E796C447-B3E8-11E4-A1A5-782BCBD7746B', '财务经理');

TRUNCATE TABLE `t_role_permission`;
INSERT INTO `t_role_permission` (`role_id`, `permission_id`) VALUES
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '-8999'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '-8997'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '-8996'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '1001'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '1002'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '1003'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '1004'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '1007'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2000'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2001'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2002'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2002-01'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2003'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2004'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2005'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2006'),
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2008'),
('38C5705B-B3E8-11E4-A1A5-782BCBD7746B', '2002'),
('38C5705B-B3E8-11E4-A1A5-782BCBD7746B', '2002-01'),
('13A411EE-B3E8-11E4-A1A5-782BCBD7746B', '1004'),
('13A411EE-B3E8-11E4-A1A5-782BCBD7746B', '2001'),
('85E8B5C2-B3E8-11E4-A1A5-782BCBD7746B', '1003'),
('85E8B5C2-B3E8-11E4-A1A5-782BCBD7746B', '2000'),
('85E8B5C2-B3E8-11E4-A1A5-782BCBD7746B', '2003'),
('E796C447-B3E8-11E4-A1A5-782BCBD7746B', '2005'),
('E796C447-B3E8-11E4-A1A5-782BCBD7746B', '2004');

TRUNCATE TABLE `t_role_user`;
INSERT INTO `t_role_user` (`role_id`, `user_id`) VALUES
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B'),
('38C5705B-B3E8-11E4-A1A5-782BCBD7746B', '073089A4-B3E6-11E4-AC63-782BCBD7746B'),
('13A411EE-B3E8-11E4-A1A5-782BCBD7746B', 'F90E470B-B3E5-11E4-AC63-782BCBD7746B'),
('85E8B5C2-B3E8-11E4-A1A5-782BCBD7746B', '70E2D355-B3E6-11E4-AC63-782BCBD7746B'),
('E796C447-B3E8-11E4-A1A5-782BCBD7746B', 'CCF6A105-B3E8-11E4-A1A5-782BCBD7746B');

TRUNCATE TABLE `t_sr_bill`;
TRUNCATE TABLE `t_sr_bill_detail`;
TRUNCATE TABLE `t_supplier`;
TRUNCATE TABLE `t_supplier_category`;
TRUNCATE TABLE `t_user`;
INSERT INTO `t_user` (`id`, `enabled`, `login_name`, `name`, `org_id`, `org_code`, `password`, `py`) VALUES
('073089A4-B3E6-11E4-AC63-782BCBD7746B', 1, 'lijingbo', '李静波', '37500493-B3E5-11E4-AC63-782BCBD7746B', '010301', 'e10adc3949ba59abbe56e057f20f883e', 'LJB'),
('54E6592A-B3E6-11E4-AC63-782BCBD7746B', 1, 'xueweixian', '薛伟先', '527369D1-B3E5-11E4-AC63-782BCBD7746B', '010101', 'e10adc3949ba59abbe56e057f20f883e', 'XWX'),
('6C2A09CD-A129-11E4-9B6A-782BCBD7746B', 1, 'admin', '系统管理员', '5EBDBE11-A129-11E4-9B6A-782BCBD7746B', '019901', '21232f297a57a5a743894a0e4a801fc3', 'XTGLY'),
('70E2D355-B3E6-11E4-AC63-782BCBD7746B', 1, 'zhouwenshuang', '周文双', '281F55E0-B3E6-11E4-AC63-782BCBD7746B', '010401', 'e10adc3949ba59abbe56e057f20f883e', 'ZWS'),
('CCF6A105-B3E8-11E4-A1A5-782BCBD7746B', 1, 'zhaoxue', '赵雪', '92FAB90F-B3E8-11E4-A1A5-782BCBD7746B', '010501', 'e10adc3949ba59abbe56e057f20f883e', 'ZX'),
('F90E470B-B3E5-11E4-AC63-782BCBD7746B', 1, 'zhangxiaodong', '张晓东', '2D496F44-B3E5-11E4-AC63-782BCBD7746B', '010201', 'e10adc3949ba59abbe56e057f20f883e', 'ZXD');

TRUNCATE TABLE `t_warehouse`;
TRUNCATE TABLE `t_ws_bill`;
TRUNCATE TABLE `t_ws_bill_detail`;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
