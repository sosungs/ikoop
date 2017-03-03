<?php

namespace Home\Service;

use Home\DAO\ICBillDAO;

/**
 * 库存盘点Service
 *
 * @author 李静波
 */
class ICBillService extends PSIBaseExService {
	private $LOG_CATEGORY = "库存盘点";

	/**
	 * 获得某个盘点单的详情
	 */
	public function icBillInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getLoginUserId();
		$params["loginUserName"] = $this->getLoginUserName();
		
		$dao = new ICBillDAO($this->db());
		
		return $dao->icBillInfo($params);
	}

	/**
	 * 新建或编辑盘点单
	 */
	public function editICBill($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$json = $params["jsonStr"];
		$bill = json_decode(html_entity_decode($json), true);
		if ($bill == null) {
			return $this->bad("传入的参数错误，不是正确的JSON格式");
		}
		
		$db = $this->db();
		$db->startTrans();
		
		$dao = new ICBillDAO($db);
		
		$id = $bill["id"];
		
		$log = null;
		
		if ($id) {
			// 编辑单据
			
			$bill["loginUserId"] = $this->getLoginUserId();
			$rc = $dao->updateICBill($bill);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$ref = $bill["ref"];
			$log = "编辑盘点单，单号：$ref";
		} else {
			// 新建单据
			
			$bill["dataOrg"] = $this->getLoginUserDataOrg();
			$bill["companyId"] = $this->getCompanyId();
			$bill["loginUserId"] = $this->getLoginUserId();
			
			$rc = $dao->addICBill($bill);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$ref = $bill["ref"];
			$log = "新建盘点单，单号：$ref";
		}
		
		// 记录业务日志
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok($id);
	}

	/**
	 * 盘点单列表
	 */
	public function icbillList($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$params["loginUserId"] = $this->getLoginUserId();
		
		$dao = new ICBillDAO($this->db());
		return $dao->icbillList($params);
	}

	/**
	 * 盘点单明细记录
	 */
	public function icBillDetailList($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$dao = new ICBillDAO($this->db());
		return $dao->icBillDetailList($params);
	}

	/**
	 * 删除盘点单
	 */
	public function deleteICBill($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		
		$db = M();
		$db->startTrans();
		
		$sql = "select ref, bill_status from t_ic_bill where id = '%s' ";
		$data = $db->query($sql, $id);
		
		if (! $data) {
			$db->rollback();
			return $this->bad("要删除的盘点单不存在");
		}
		
		$ref = $data[0]["ref"];
		$billStatus = $data[0]["bill_status"];
		
		if ($billStatus != 0) {
			$db->rollback();
			return $this->bad("盘点单(单号：$ref)已经提交，不能被删除");
		}
		
		$sql = "delete from t_ic_bill_detail where icbill_id = '%s' ";
		$rc = $db->execute($sql, $id);
		if ($rc === false) {
			$db->rollback();
			return $this->sqlError(__LINE__);
		}
		
		$sql = "delete from t_ic_bill where id = '%s' ";
		$rc = $db->execute($sql, $id);
		if ($rc === false) {
			$db->rollback();
			return $this->sqlError(__LINE__);
		}
		
		$bs = new BizlogService();
		$log = "删除盘点单，单号：$ref";
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	/**
	 * 提交盘点单
	 */
	public function commitICBill($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		$db = M();
		$db->startTrans();
		
		$sql = "select ref, bill_status, warehouse_id, bizdt, biz_user_id 
					from t_ic_bill 
					where id = '%s' ";
		$data = $db->query($sql, $id);
		if (! $data) {
			$db->rollback();
			return $this->bad("要提交的盘点单不存在");
		}
		$ref = $data[0]["ref"];
		$billStatus = $data[0]["bill_status"];
		if ($billStatus != 0) {
			$db->rollback();
			return $this->bad("盘点单(单号：$ref)已经提交，不能再次提交");
		}
		$warehouseId = $data[0]["warehouse_id"];
		$bizDT = date("Y-m-d", strtotime($data[0]["bizdt"]));
		$bizUserId = $data[0]["biz_user_id"];
		
		$sql = "select name, inited from t_warehouse where id = '%s' ";
		$data = $db->query($sql, $warehouseId);
		if (! $data) {
			$db->rollback();
			return $this->bad("要盘点的仓库不存在");
		}
		$inited = $data[0]["inited"];
		$warehouseName = $data[0]["name"];
		if ($inited != 1) {
			$db->rollback();
			return $this->bad("仓库[$warehouseName]还没有建账，无法做盘点操作");
		}
		
		$sql = "select name from t_user where id = '%s' ";
		$data = $db->query($sql, $bizUserId);
		if (! $data) {
			$db->rollback();
			return $this->bad("业务人员不存在，无法完成提交");
		}
		
		$sql = "select goods_id, goods_count, goods_money
					from t_ic_bill_detail
					where icbill_id = '%s' 
					order by show_order ";
		$items = $db->query($sql, $id);
		if (! $items) {
			$db->rollback();
			return $this->bad("盘点单没有明细信息，无法完成提交");
		}
		
		foreach ( $items as $i => $v ) {
			$goodsId = $v["goods_id"];
			$goodsCount = $v["goods_count"];
			$goodsMoney = $v["goods_money"];
			
			// 检查商品是否存在
			$sql = "select code, name, spec from t_goods where id = '%s' ";
			$data = $db->query($sql, $goodsId);
			if (! $data) {
				$db->rollback();
				$index = $i + 1;
				return $this->bad("第{$index}条记录的商品不存在，无法完成提交");
			}
			
			if ($goodsCount < 0) {
				$db->rollback();
				$index = $i + 1;
				return $this->bad("第{$index}条记录的商品盘点后库存数量不能为负数");
			}
			if ($goodsMoney < 0) {
				$db->rollback();
				$index = $i + 1;
				return $this->bad("第{$index}条记录的商品盘点后库存金额不能为负数");
			}
			if ($goodsCount == 0) {
				if ($goodsMoney != 0) {
					$db->rollback();
					$index = $i + 1;
					return $this->bad("第{$index}条记录的商品盘点后库存数量为0的时候，库存金额也必须为0");
				}
			}
			
			$sql = "select balance_count, balance_money, in_count, in_money, out_count, out_money 
						from t_inventory
						where warehouse_id = '%s' and goods_id = '%s' ";
			$data = $db->query($sql, $warehouseId, $goodsId);
			if (! $data) {
				// 这种情况是：没有库存，做盘盈入库
				$inCount = $goodsCount;
				$inMoney = $goodsMoney;
				$inPrice = 0;
				if ($inCount != 0) {
					$inPrice = $inMoney / $inCount;
				}
				
				// 库存总账
				$sql = "insert into t_inventory(in_count, in_price, in_money, balance_count, balance_price,
							balance_money, warehouse_id, goods_id)
							values (%d, %f, %f, %d, %f, %f, '%s', '%s')";
				$rc = $db->execute($sql, $inCount, $inPrice, $inMoney, $inCount, $inPrice, $inMoney, 
						$warehouseId, $goodsId);
				if ($rc === false) {
					$db->rollback();
					return $this->sqlError(__LINE__);
				}
				
				// 库存明细账
				$sql = "insert into t_inventory_detail(in_count, in_price, in_money, balance_count, balance_price,
							balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
							ref_type)
							values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘盈入库')";
				$rc = $db->execute($sql, $inCount, $inPrice, $inMoney, $inCount, $inPrice, $inMoney, 
						$warehouseId, $goodsId, $bizDT, $bizUserId, $ref);
				if ($rc === false) {
					$db->rollback();
					return $this->sqlError(__LINE__);
				}
			} else {
				$balanceCount = $data[0]["balance_count"];
				$balanceMoney = $data[0]["balance_money"];
				
				if ($goodsCount > $balanceCount) {
					// 盘盈入库
					$inCount = $goodsCount - $balanceCount;
					$inMoney = $goodsMoney - $balanceMoney;
					$inPrice = $inMoney / $inCount;
					$balanceCount = $goodsCount;
					$balanceMoney = $goodsMoney;
					$balancePrice = $balanceMoney / $balanceCount;
					$totalInCount = $data[0]["in_count"] + $inCount;
					$totalInMoney = $data[0]["in_money"] + $inMoney;
					$totalInPrice = $totalInMoney / $totalInCount;
					
					// 库存总账
					$sql = "update t_inventory
								set in_count = %d, in_price = %f, in_money = %f, 
								    balance_count = %d, balance_price = %f,
							        balance_money = %f
								where warehouse_id = '%s' and goods_id = '%s' ";
					$rc = $db->execute($sql, $totalInCount, $totalInPrice, $totalInMoney, 
							$balanceCount, $balancePrice, $balanceMoney, $warehouseId, $goodsId);
					if ($rc === false) {
						$db->rollback();
						return $this->sqlError(__LINE__);
					}
					
					// 库存明细账
					$sql = "insert into t_inventory_detail(in_count, in_price, in_money, balance_count, balance_price,
							balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
							ref_type)
							values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘盈入库')";
					$rc = $db->execute($sql, $inCount, $inPrice, $inMoney, $balanceCount, 
							$balancePrice, $balanceMoney, $warehouseId, $goodsId, $bizDT, $bizUserId, 
							$ref);
					if ($rc === false) {
						$db->rollback();
						return $this->sqlError(__LINE__);
					}
				} else {
					// 盘亏出库
					$outCount = $balanceCount - $goodsCount;
					$outMoney = $balanceMoney - $goodsMoney;
					$outPrice = 0;
					if ($outCount != 0) {
						$outPrice = $outMoney / $outCount;
					}
					$balanceCount = $goodsCount;
					$balanceMoney = $goodsMoney;
					$balancePrice = 0;
					if ($balanceCount != 0) {
						$balancePrice = $balanceMoney / $balanceCount;
					}
					
					$totalOutCount = $data[0]["out_count"] + $outCount;
					$totalOutMoney = $data[0]["out_money"] + $outMoney;
					$totalOutPrice = $totalOutMoney / $totalOutCount;
					
					// 库存总账
					$sql = "update t_inventory
								set out_count = %d, out_price = %f, out_money = %f, 
								    balance_count = %d, balance_price = %f,
							        balance_money = %f
								where warehouse_id = '%s' and goods_id = '%s' ";
					$rc = $db->execute($sql, $totalOutCount, $totalOutPrice, $totalOutMoney, 
							$balanceCount, $balancePrice, $balanceMoney, $warehouseId, $goodsId);
					if ($rc === false) {
						$db->rollback();
						return $this->sqlError(__LINE__);
					}
					
					// 库存明细账
					$sql = "insert into t_inventory_detail(out_count, out_price, out_money, balance_count, balance_price,
							balance_money, warehouse_id, goods_id, biz_date, biz_user_id, date_created, ref_number,
							ref_type)
							values (%d, %f, %f, %d, %f, %f, '%s', '%s', '%s', '%s', now(), '%s', '库存盘点-盘亏出库')";
					$rc = $db->execute($sql, $outCount, $outPrice, $outMoney, $balanceCount, 
							$balancePrice, $balanceMoney, $warehouseId, $goodsId, $bizDT, $bizUserId, 
							$ref);
					if ($rc === false) {
						$db->rollback();
						return $this->sqlError(__LINE__);
					}
				}
			}
		}
		
		// 修改单据本身状态
		$sql = "update t_ic_bill
				set bill_status = 1000
				where id = '%s' ";
		$rc = $db->execute($sql, $id);
		if ($rc === false) {
			$db->rollback();
			return $this->sqlError(__LINE__);
		}
		
		// 记录业务日志
		$bs = new BizlogService();
		$log = "提交盘点单，单号：$ref";
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok($id);
	}
}