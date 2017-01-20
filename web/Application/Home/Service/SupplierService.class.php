<?php

namespace Home\Service;

use Home\DAO\SupplierDAO;
use Home\Service\BizlogService;
use Home\Service\IdGenService;

/**
 * 供应商档案Service
 *
 * @author 李静波
 */
class SupplierService extends PSIBaseService {
	private $LOG_CATEGORY = "基础数据-供应商档案";

	/**
	 * 供应商分类列表
	 */
	public function categoryList($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$us = new UserService();
		$params["loginUserId"] = $us->getLoginUserId();
		
		$dao = new SupplierDAO();
		return $dao->categoryList($params);
	}

	/**
	 * 某个分类下的供应商档案列表
	 */
	public function supplierList($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$us = new UserService();
		$params["loginUserId"] = $us->getLoginUserId();
		
		$dao = new SupplierDAO();
		return $dao->supplierList($params);
	}

	/**
	 * 新建或编辑供应商分类
	 */
	public function editCategory($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		$code = $params["code"];
		$name = $params["name"];
		
		$db = M();
		$db->startTrans();
		
		$dao = new SupplierDAO($db);
		
		$log = null;
		
		if ($id) {
			// 编辑
			$rc = $dao->updateSupplierCategory($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "编辑供应商分类: 编码 = $code, 分类名 = $name";
		} else {
			// 新增
			$idGen = new IdGenService();
			$id = $idGen->newId();
			$params["id"] = $id;
			
			$us = new UserService();
			$params["dataOrg"] = $us->getLoginUserDataOrg();
			$params["companyId"] = $us->getCompanyId();
			
			$rc = $dao->addSupplierCategory($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "新增供应商分类：编码 = $code, 分类名 = $name";
		}
		
		// 记录业务日志
		if ($log) {
			$bs = new BizlogService($db);
			$bs->insertBizlog($log, $this->LOG_CATEGORY);
		}
		
		$db->commit();
		
		return $this->ok($id);
	}

	/**
	 * 删除供应商分类
	 */
	public function deleteCategory($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		
		$db = M();
		$db->startTrans();
		$dao = new SupplierDAO($db);
		
		$category = $dao->getSupplierCategoryById($id);
		if (! $category) {
			$db->rollback();
			return $this->bad("要删除的分类不存在");
		}
		
		$params["name"] = $category["name"];
		
		$rc = $dao->deleteSupplierCategory($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$log = "删除供应商分类： 编码 = {$category['code']}, 分类名称 = {$category['name']}";
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	/**
	 * 新建或编辑供应商档案
	 */
	public function editSupplier($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		$code = $params["code"];
		$name = $params["name"];
		
		$ps = new PinyinService();
		$py = $ps->toPY($name);
		$params["py"] = $py;
		
		$categoryId = $params["categoryId"];
		
		$db = M();
		$db->startTrans();
		
		$dao = new SupplierDAO($db);
		
		$category = $dao->getSupplierCategoryById($categoryId);
		if (! $category) {
			$db->rollback();
			return $this->bad("供应商分类不存在");
		}
		
		$log = null;
		
		if ($id) {
			// 编辑
			$rc = $dao->updateSupplier($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "编辑供应商：编码 = $code, 名称 = $name";
		} else {
			// 新增
			$idGen = new IdGenService();
			$id = $idGen->newId();
			$params["id"] = $id;
			
			$us = new UserService();
			$params["dataOrg"] = $us->getLoginUserDataOrg();
			$params["companyId"] = $us->getCompanyId();
			
			$rc = $dao->addSupplier($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "新增供应商：编码 = {$code}, 名称 = {$name}";
		}
		
		// 处理应付期初余额
		$rc = $dao->initPayables($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		// 记录业务日志
		if ($log) {
			$bs = new BizlogService($db);
			$bs->insertBizlog($log, $this->LOG_CATEGORY);
		}
		
		$db->commit();
		
		return $this->ok($id);
	}

	/**
	 * 删除供应商
	 */
	public function deleteSupplier($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		
		$db = M();
		$db->startTrans();
		
		$dao = new SupplierDAO($db);
		
		$supplier = $dao->getSupplierById($id);
		
		if (! $supplier) {
			$db->rollback();
			return $this->bad("要删除的供应商档案不存在");
		}
		$code = $supplier["code"];
		$name = $supplier["name"];
		
		$rc = $dao->deleteSupplier($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$log = "删除供应商档案：编码 = {$code},  名称 = {$name}";
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	/**
	 * 供应商字段， 查询数据
	 */
	public function queryData($queryKey) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$us = new UserService();
		
		$params = array(
				"queryKey" => $queryKey,
				"loginUserId" => $us->getLoginUserId()
		);
		
		$dao = new SupplierDAO();
		return $dao->queryData($params);
	}

	/**
	 * 获得某个供应商档案的详情
	 */
	public function supplierInfo($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$id = $params["id"];
		
		$result = array();
		
		$db = M();
		$sql = "select category_id, code, name, contact01, qq01, mobile01, tel01,
					contact02, qq02, mobile02, tel02, address, address_shipping,
					init_payables, init_payables_dt,
					bank_name, bank_account, tax_number, fax, note
				from t_supplier
				where id = '%s' ";
		$data = $db->query($sql, $id);
		if ($data) {
			$result["categoryId"] = $data[0]["category_id"];
			$result["code"] = $data[0]["code"];
			$result["name"] = $data[0]["name"];
			$result["contact01"] = $data[0]["contact01"];
			$result["qq01"] = $data[0]["qq01"];
			$result["mobile01"] = $data[0]["mobile01"];
			$result["tel01"] = $data[0]["tel01"];
			$result["contact02"] = $data[0]["contact02"];
			$result["qq02"] = $data[0]["qq02"];
			$result["mobile02"] = $data[0]["mobile02"];
			$result["tel02"] = $data[0]["tel02"];
			$result["address"] = $data[0]["address"];
			$result["addressShipping"] = $data[0]["address_shipping"];
			$result["initPayables"] = $data[0]["init_payables"];
			$d = $data[0]["init_payables_dt"];
			if ($d) {
				$result["initPayablesDT"] = $this->toYMD($d);
			}
			$result["bankName"] = $data[0]["bank_name"];
			$result["bankAccount"] = $data[0]["bank_account"];
			$result["tax"] = $data[0]["tax_number"];
			$result["fax"] = $data[0]["fax"];
			$result["note"] = $data[0]["note"];
		}
		
		return $result;
	}

	/**
	 * 判断供应商是否存在
	 */
	public function supplierExists($supplierId, $db) {
		if (! $db) {
			$db = M();
		}
		
		$sql = "select count(*) as cnt from t_supplier where id = '%s' ";
		$data = $db->query($sql, $supplierId);
		return $data[0]["cnt"] == 1;
	}

	/**
	 * 根据供应商Id查询供应商名称
	 */
	public function getSupplierNameById($supplierId, $db) {
		if (! $db) {
			$db = M();
		}
		
		$sql = "select name from t_supplier where id = '%s' ";
		$data = $db->query($sql, $supplierId);
		if ($data) {
			return $data[0]["name"];
		} else {
			return "";
		}
	}
}