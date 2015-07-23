SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

TRUNCATE TABLE `t_fid`;
INSERT INTO `t_fid` (`fid`, `name`) VALUES
('-9999', '重新登录'),
('-9997', '首页'),
('-9996', '修改我的密码'),
('-9995', '帮助'),
('-9994', '关于'),
('-9993', '购买商业服务'),
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
('2008', '业务设置'),
('2009', '库间调拨');

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
('0302', '库存建账', '2000', '03', 2),
('0303', '库间调拨', '2009', '03', 3),
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
('0904', '业务设置', '2008', '09', 4),
('10', '帮助', NULL, NULL, 10),
('1001', '使用帮助', '-9995', '10', 1),
('1002', '购买商业服务', '-9993', '10', 2),
('1003', '关于PSI', '-9994', '10', 3);

TRUNCATE TABLE `t_org`;
INSERT INTO `t_org` (`id`, `full_name`, `name`, `org_code`, `parent_id`) VALUES
('4D74E1E4-A129-11E4-9B6A-782BCBD7746B', '公司', '公司', '01', NULL),
('5EBDBE11-A129-11E4-9B6A-782BCBD7746B', '公司\\信息部', '信息部', '0199', '4D74E1E4-A129-11E4-9B6A-782BCBD7746B');

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
('2008', '2008', '业务设置', '业务设置'),
('2009', '2009', '库间调拨', '库间调拨');

TRUNCATE TABLE `t_role`;
INSERT INTO `t_role` (`id`, `name`) VALUES
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '系统管理');

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
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '2009');

TRUNCATE TABLE `t_role_user`;
INSERT INTO `t_role_user` (`role_id`, `user_id`) VALUES
('A83F617E-A153-11E4-A9B8-782BCBD7746B', '6C2A09CD-A129-11E4-9B6A-782BCBD7746B');

TRUNCATE TABLE `t_user`;
INSERT INTO `t_user` (`id`, `enabled`, `login_name`, `name`, `org_id`, `org_code`, `password`, `py`) VALUES
('6C2A09CD-A129-11E4-9B6A-782BCBD7746B', '1', 'admin', '系统管理员', '5EBDBE11-A129-11E4-9B6A-782BCBD7746B', '019901', '21232f297a57a5a743894a0e4a801fc3', 'XTGLY');

TRUNCATE TABLE `t_config`;
INSERT INTO `t_config` (`id`, `name`, `value`, `note`) VALUES
('2002-01', '销售出库单允许编辑销售单价', '0', '当允许编辑的时候，还需要给用户赋予权限[销售出库单允许编辑销售单价]'),
('2001-01', '采购入库默认仓库', '', ''),
('2002-02', '销售出库默认仓库', '', ''),
('1001-01', '商品采购和销售分别使用不同的计量单位', '0', ''),
('1003-01', '仓库需指定组织机构', '0', '当仓库需要指定组织机构的时候，就意味着可以控制仓库的使用人');

TRUNCATE TABLE `t_psi_db_version`;
INSERT INTO `t_psi_db_version` (`db_version`, `update_dt`) VALUES
('20150723-001', '2015-07-23');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
