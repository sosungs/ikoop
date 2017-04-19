<?php

namespace Home\Service;

use Home\DAO\PWBillDAO;
use Home\DAO\PRBillDAO;

/**
 * 查看单据Service
 *
 * @author 李静波
 */
class BillViewService extends PSIBaseExService {

	/**
	 * 由单号查询采购入库单信息
	 *
	 * @param string $ref
	 *        	采购入库单单号
	 * @return array|NULL
	 */
	public function pwBillInfo($ref) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$dao = new PWBillDAO($this->db());
		return $dao->getFullBillDataByRef($ref);
	}

	/**
	 * 由单号查询销售出库单信息
	 *
	 * @param array $params        	
	 * @return array
	 */
	public function wsBillInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$ref = $params["ref"];
		
		$result = array();
		
		$db = $this->db();
		$sql = "select w.id, w.bizdt, c.name as customer_name,
					  u.name as biz_user_name,
					  h.name as warehouse_name, w.memo
					from t_ws_bill w, t_customer c, t_user u, t_warehouse h
					where w.customer_id = c.id and w.biz_user_id = u.id
					  and w.warehouse_id = h.id
					  and w.ref = '%s' ";
		$data = $db->query($sql, $ref);
		if ($data) {
			$id = $data[0]["id"];
			
			$result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
			$result["customerName"] = $data[0]["customer_name"];
			$result["warehouseName"] = $data[0]["warehouse_name"];
			$result["bizUserName"] = $data[0]["biz_user_name"];
			$result["memo"] = $data[0]["memo"];
			
			// 明细表
			$sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, d.goods_count,
					d.goods_price, d.goods_money, d.sn_note, d.memo
					from t_ws_bill_detail d, t_goods g, t_goods_unit u
					where d.wsbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
					order by d.show_order";
			$data = $db->query($sql, $id);
			$items = array();
			foreach ( $data as $i => $v ) {
				$items[$i]["id"] = $v["id"];
				$items[$i]["goodsId"] = $v["goods_id"];
				$items[$i]["goodsCode"] = $v["code"];
				$items[$i]["goodsName"] = $v["name"];
				$items[$i]["goodsSpec"] = $v["spec"];
				$items[$i]["unitName"] = $v["unit_name"];
				$items[$i]["goodsCount"] = $v["goods_count"];
				$items[$i]["goodsPrice"] = $v["goods_price"];
				$items[$i]["goodsMoney"] = $v["goods_money"];
				$items[$i]["sn"] = $v["sn_note"];
				$items[$i]["memo"] = $v["memo"];
			}
			
			$result["items"] = $items;
		}
		
		return $result;
	}

	/**
	 * 由单号查询采购退货出库单
	 *
	 * @param string $ref        	
	 * @return array|NULL
	 */
	public function prBillInfo($ref) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$dao = new PRBillDAO($this->db());
		return $dao->getFullBillDataByRef($ref);
	}

	/**
	 * 由单号查询销售退货入库单信息
	 *
	 * @param array $params        	
	 * @return array
	 */
	public function srBillInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$ref = $params["ref"];
		
		$db = $this->db();
		$result = array();
		$sql = "select w.id, w.bizdt, c.name as customer_name,
				 u.name as biz_user_name,
				 h.name as warehouse_name, wsBill.ref as ws_bill_ref
				 from t_sr_bill w, t_customer c, t_user u, t_warehouse h, t_ws_bill wsBill
				 where w.customer_id = c.id and w.biz_user_id = u.id
				 and w.warehouse_id = h.id
				 and w.ref = '%s' and wsBill.id = w.ws_bill_id";
		$data = $db->query($sql, $ref);
		if ($data) {
			$id = $data[0]["id"];
			
			$result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
			$result["customerName"] = $data[0]["customer_name"];
			$result["warehouseName"] = $data[0]["warehouse_name"];
			$result["bizUserName"] = $data[0]["biz_user_name"];
			$result["wsBillRef"] = $data[0]["ws_bill_ref"];
			
			$sql = "select d.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, d.goods_count,
					d.goods_price, d.goods_money,
					d.rejection_goods_count, d.rejection_goods_price, d.rejection_sale_money,
					d.wsbilldetail_id, d.sn_note
					 from t_sr_bill_detail d, t_goods g, t_goods_unit u
					 where d.srbill_id = '%s' and d.goods_id = g.id and g.unit_id = u.id
					 order by d.show_order";
			$data = $db->query($sql, $id);
			$items = array();
			foreach ( $data as $i => $v ) {
				$items[$i]["id"] = $v["wsbilldetail_id"];
				$items[$i]["goodsId"] = $v["goods_id"];
				$items[$i]["goodsCode"] = $v["code"];
				$items[$i]["goodsName"] = $v["name"];
				$items[$i]["goodsSpec"] = $v["spec"];
				$items[$i]["unitName"] = $v["unit_name"];
				$items[$i]["goodsCount"] = $v["goods_count"];
				$items[$i]["goodsPrice"] = $v["goods_price"];
				$items[$i]["goodsMoney"] = $v["goods_money"];
				$items[$i]["rejCount"] = $v["rejection_goods_count"];
				$items[$i]["rejPrice"] = $v["rejection_goods_price"];
				$items[$i]["rejMoney"] = $v["rejection_sale_money"];
				$items[$i]["sn"] = $v["sn_note"];
			}
			
			$result["items"] = $items;
		}
		
		return $result;
	}

	/**
	 * 由单号查询调拨单信息
	 *
	 * @param array $params        	
	 * @return array
	 */
	public function itBillInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$ref = $params["ref"];
		
		$result = array();
		
		$db = $this->db();
		$sql = "select t.id, t.bizdt, u.name as biz_user_name,
					wf.name as from_warehouse_name,
					wt.name as to_warehouse_name
				from t_it_bill t, t_user u, t_warehouse wf, t_warehouse wt
				where t.ref = '%s' and t.biz_user_id = u.id
				      and t.from_warehouse_id = wf.id
				      and t.to_warehouse_id = wt.id";
		$data = $db->query($sql, $ref);
		if (! $data) {
			return $result;
		}
		
		$id = $data[0]["id"];
		$result["bizUserName"] = $data[0]["biz_user_name"];
		$result["bizDT"] = date("Y-m-d", strtotime($data[0]["bizdt"]));
		$result["fromWarehouseName"] = $data[0]["from_warehouse_name"];
		$result["toWarehouseName"] = $data[0]["to_warehouse_name"];
		
		$items = array();
		$sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name, t.goods_count
				from t_it_bill_detail t, t_goods g, t_goods_unit u
				where t.itbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
				order by t.show_order ";
		
		$data = $db->query($sql, $id);
		foreach ( $data as $i => $v ) {
			$items[$i]["id"] = $v["id"];
			$items[$i]["goodsId"] = $v["goods_id"];
			$items[$i]["goodsCode"] = $v["code"];
			$items[$i]["goodsName"] = $v["name"];
			$items[$i]["goodsSpec"] = $v["spec"];
			$items[$i]["unitName"] = $v["unit_name"];
			$items[$i]["goodsCount"] = $v["goods_count"];
		}
		
		$result["items"] = $items;
		
		return $result;
	}

	/**
	 * 由单号查询盘点单信息
	 *
	 * @param array $params        	
	 * @return array
	 */
	public function icBillInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$ref = $params["ref"];
		
		$result = array();
		
		$db = $this->db();
		$sql = "select t.id, t.bizdt, u.name as biz_user_name,
					w.name as warehouse_name
				from t_ic_bill t, t_user u, t_warehouse w
				where t.ref = '%s' and t.biz_user_id = u.id
				      and t.warehouse_id = w.id";
		$data = $db->query($sql, $ref);
		if (! $data) {
			return $result;
		}
		
		$id = $data[0]["id"];
		$result["bizUserName"] = $data[0]["biz_user_name"];
		$result["bizDT"] = $this->toYMD($data[0]["bizdt"]);
		$result["warehouseName"] = $data[0]["warehouse_name"];
		
		$items = array();
		$sql = "select t.id, g.id as goods_id, g.code, g.name, g.spec, u.name as unit_name,
						t.goods_count, t.goods_money
				from t_ic_bill_detail t, t_goods g, t_goods_unit u
				where t.icbill_id = '%s' and t.goods_id = g.id and g.unit_id = u.id
				order by t.show_order ";
		
		$data = $db->query($sql, $id);
		foreach ( $data as $i => $v ) {
			$items[$i]["id"] = $v["id"];
			$items[$i]["goodsId"] = $v["goods_id"];
			$items[$i]["goodsCode"] = $v["code"];
			$items[$i]["goodsName"] = $v["name"];
			$items[$i]["goodsSpec"] = $v["spec"];
			$items[$i]["unitName"] = $v["unit_name"];
			$items[$i]["goodsCount"] = $v["goods_count"];
			$items[$i]["goodsMoney"] = $v["goods_money"];
		}
		
		$result["items"] = $items;
		
		return $result;
	}
}