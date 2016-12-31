<?php

namespace Home\Service;

use Home\Common\DemoConst;
use Home\Common\FIdConst;
use Home\DAO\UserDAO;
use Home\DAO\OrgDAO;

/**
 * 用户Service
 *
 * @author 李静波
 */
class UserService extends PSIBaseService {
	private $LOG_CATEGORY = "用户管理";

	/**
	 * 演示环境中显示在登录窗口上的提示文字
	 *
	 * @return string
	 */
	public function getDemoLoginInfo() {
		if ($this->isDemo()) {
			return "您当前处于演示环境，默认的登录名和密码均为 admin <br/>更多帮助请点击 [帮助] 按钮来查看 <br /><div style='color:red'>请勿在演示环境中保存正式数据，演示数据库通常每天在21:00后会清空一次</div>";
		} else {
			return "";
		}
	}

	/**
	 * 判断当前用户是否有$fid对应的权限
	 *
	 * @param string $fid
	 *        	fid
	 * @return boolean true：有对应的权限
	 */
	public function hasPermission($fid = null) {
		$result = session("loginUserId") != null;
		if (! $result) {
			return false;
		}
		
		$userId = $this->getLoginUserId();
		
		if ($userId == DemoConst::ADMIN_USER_ID) {
			// admin 用户是超级管理员
			return true;
		}
		
		// 判断用户是否被禁用
		// 被禁用的用户，视为没有权限
		$ud = new UserDAO();
		if ($ud->isDisabled($userId)) {
			return false;
		}
		
		// 修改我的密码，重新登录，首页，使用帮助，关于，购买商业服务，这六个功能对所有的在线用户均不需要特别的权限
		$idList = array(
				FIdConst::CHANGE_MY_PASSWORD,
				FIdConst::RELOGIN,
				FIdConst::HOME,
				FIdConst::HELP,
				FIdConst::ABOUT,
				FIdConst::PSI_SERVICE
		);
		if ($fid == null || in_array($fid, $idList)) {
			return $result;
		}
		
		return $ud->hasPermission($userId, $fid);
	}

	/**
	 * 当前登录用户的id
	 *
	 * @return string|NULL
	 */
	public function getLoginUserId() {
		return session("loginUserId");
	}

	/**
	 * 当前登录用户的姓名
	 *
	 * @return string
	 */
	public function getLoginUserName() {
		$dao = new UserDAO();
		return $dao->getLoginUserName($this->getLoginUserId());
	}

	/**
	 * 当前登录用户带组织机构的用户全名
	 *
	 * @return string
	 */
	public function getLoignUserNameWithOrgFullName() {
		$dao = new UserDAO();
		return $dao->getLoignUserNameWithOrgFullName($this->getLoginUserId());
	}

	/**
	 * 获得当前登录用户的登录名
	 *
	 * @return string
	 */
	public function getLoginName() {
		$dao = new UserDAO();
		return $dao->getLoginName($this->getLoginUserId());
	}

	/**
	 * 登录PSI
	 */
	public function doLogin($params) {
		$dao = new UserDAO();
		$loginUserId = $dao->doLogin($params);
		
		if ($loginUserId) {
			session("loginUserId", $loginUserId);
			
			$bls = new BizlogService();
			$bls->insertBizlog("登录系统");
			return $this->ok();
		} else {
			return $this->bad("用户名或者密码错误");
		}
	}

	private function allOrgsInternal($parentId, $db) {
		$result = array();
		$sql = "select id, name, org_code, full_name, data_org 
				from t_org 
				where parent_id = '%s' 
				order by org_code";
		$data = $db->query($sql, $parentId);
		foreach ( $data as $i => $v ) {
			$result[$i]["id"] = $v["id"];
			$result[$i]["text"] = $v["name"];
			$result[$i]["orgCode"] = $v["org_code"];
			$result[$i]["fullName"] = $v["full_name"];
			$result[$i]["dataOrg"] = $v["data_org"];
			
			$c2 = $this->allOrgsInternal($v["id"], $db); // 递归调用自己
			
			$result[$i]["children"] = $c2;
			$result[$i]["leaf"] = count($c2) == 0;
			$result[$i]["expanded"] = true;
		}
		
		return $result;
	}

	public function allOrgs() {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$us = new UserService();
		$params = array(
				"loginUserId" => $us->getLoginUserId()
		);
		
		$dao = new OrgDAO();
		
		return $dao->allOrgs($params);
	}

	public function users($params) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$dao = new UserDAO();
		return $dao->users($params);
	}

	/**
	 * 做类似这种增长 '0101' => '0102'，组织机构的数据域+1
	 */
	private function incDataOrg($dataOrg) {
		$pre = substr($dataOrg, 0, strlen($dataOrg) - 2);
		$seed = intval(substr($dataOrg, - 2)) + 1;
		
		return $pre . str_pad($seed, 2, "0", STR_PAD_LEFT);
	}

	/**
	 * 做类似这种增长 '01010001' => '01010002', 用户的数据域+1
	 */
	private function incDataOrgForUser($dataOrg) {
		$pre = substr($dataOrg, 0, strlen($dataOrg) - 4);
		$seed = intval(substr($dataOrg, - 4)) + 1;
		
		return $pre . str_pad($seed, 4, "0", STR_PAD_LEFT);
	}

	private function modifyFullName($db, $id) {
		$sql = "select full_name from t_org where id = '%s' ";
		$data = $db->query($sql, $id);
		
		if (! $data) {
			return true;
		}
		
		$fullName = $data[0]["full_name"];
		
		$sql = "select id, name from t_org where parent_id = '%s' ";
		$data = $db->query($sql, $id);
		foreach ( $data as $v ) {
			$idChild = $v["id"];
			$nameChild = $v["name"];
			$fullNameChild = $fullName . "\\" . $nameChild;
			$sql = "update t_org set full_name = '%s' where id = '%s' ";
			$rc = $db->execute($sql, $fullNameChild, $idChild);
			if ($rc === false) {
				return false;
			}
			
			$rc = $this->modifyFullName($db, $idChild); // 递归调用自身
			if ($rc === false) {
				return false;
			}
		}
		
		return true;
	}

	private function modifyDataOrg($db, $parentId, $id) {
		// 修改自身的数据域
		$dataOrg = "";
		if ($parentId == null) {
			$sql = "select data_org from t_org 
					where parent_id is null and id <> '%s'
					order by data_org desc limit 1";
			$data = $db->query($sql, $id);
			if (! $data) {
				$dataOrg = "01";
			} else {
				$dataOrg = $this->incDataOrg($data[0]["data_org"]);
			}
		} else {
			$sql = "select data_org from t_org 
					where parent_id = '%s' and id <> '%s'
					order by data_org desc limit 1";
			$data = $db->query($sql, $parentId, $id);
			if ($data) {
				$dataOrg = $this->incDataOrg($data[0]["data_org"]);
			} else {
				$sql = "select data_org from t_org where id = '%s' ";
				$data = $db->query($sql, $parentId);
				$dataOrg = $data[0]["data_org"] . "01";
			}
		}
		
		$sql = "update t_org
				set data_org = '%s'
				where id = '%s' ";
		$rc = $db->execute($sql, $dataOrg, $id);
		if ($rc === false) {
			return false;
		}
		
		// 修改 人员的数据域
		$sql = "select id from t_user
				where org_id = '%s'
				order by org_code ";
		$data = $db->query($sql, $id);
		foreach ( $data as $i => $v ) {
			$userId = $v["id"];
			$index = str_pad($i + 1, 4, "0", STR_PAD_LEFT);
			$udo = $dataOrg . $index;
			
			$sql = "update t_user
					set data_org = '%s'
					where id = '%s' ";
			$rc = $db->execute($sql, $udo, $userId);
			if ($rc === false) {
				return false;
			}
		}
		
		// 修改下级组织机构的数据域
		$rc = $this->modifySubDataOrg($db, $dataOrg, $id);
		
		if ($rc === false) {
			return false;
		}
		
		return true;
	}

	private function modifySubDataOrg($db, $parentDataOrg, $parentId) {
		$sql = "select id from t_org where parent_id = '%s' order by org_code";
		$data = $db->query($sql, $parentId);
		foreach ( $data as $i => $v ) {
			$subId = $v["id"];
			
			$next = str_pad($i + 1, 2, "0", STR_PAD_LEFT);
			$dataOrg = $parentDataOrg . $next;
			$sql = "update t_org
					set data_org = '%s'
					where id = '%s' ";
			$db->execute($sql, $dataOrg, $subId);
			
			// 修改该组织机构的人员的数据域
			$sql = "select id from t_user
				where org_id = '%s'
				order by org_code ";
			$udata = $db->query($sql, $subId);
			foreach ( $udata as $j => $u ) {
				$userId = $u["id"];
				$index = str_pad($j + 1, 4, "0", STR_PAD_LEFT);
				$udo = $dataOrg . $index;
				
				$sql = "update t_user
					set data_org = '%s'
					where id = '%s' ";
				$rc = $db->execute($sql, $udo, $userId);
				if ($rc === false) {
					return false;
				}
			}
			
			$rc = $this->modifySubDataOrg($db, $dataOrg, $subId); // 递归调用自身
			if ($rc === false) {
				return false;
			}
		}
		
		return true;
	}

	public function editOrg($id, $name, $parentId, $orgCode) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		if ($this->isDemo()) {
			if ($id == DemoConst::ORG_COMPANY_ID) {
				return $this->bad("在演示环境下，组织机构[公司]不希望被您修改，请见谅");
			}
			if ($id == DemoConst::ORG_INFODEPT_ID) {
				return $this->bad("在演示环境下，组织机构[信息部]不希望被您修改，请见谅");
			}
		}
		
		$params = array(
				"id" => $id,
				"name" => $name,
				"parentId" => $parentId,
				"orgCode" => $orgCode
		);
		
		$db = M();
		$db->startTrans();
		
		$log = null;
		
		$dao = new OrgDAO($db);
		
		if ($id) {
			$rc = $dao->updateOrg($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			$log = "编辑组织机构：名称 = {$name} 编码 = {$orgCode}";
		} else {
			// 新增
			$idGenService = new IdGenService();
			$id = $idGenService->newId();
			
			$params["id"] = $id;
			
			$rc = $dao->addOrg($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "新增组织机构：名称 = {$name} 编码 = {$orgCode}";
		}
		
		// 记录业务日志
		if ($log) {
			$bs = new BizlogService($db);
			$bs->insertBizlog($log, $this->LOG_CATEGORY);
		}
		
		$db->commit();
		
		return $this->ok($id);
	}

	public function orgParentName($id) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$dao = new OrgDAO();
		return $dao->orgParentName($id);
	}

	public function deleteOrg($id) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		if ($this->isDemo()) {
			if ($id == DemoConst::ORG_COMPANY_ID) {
				return $this->bad("在演示环境下，组织机构[公司]不希望被您删除，请见谅");
			}
			if ($id == DemoConst::ORG_INFODEPT_ID) {
				return $this->bad("在演示环境下，组织机构[信息部]不希望被您删除，请见谅");
			}
		}
		
		$db = M();
		$db->startTrans();
		
		$dao = new OrgDAO($db);
		$org = $dao->getOrgById($id);
		if (! $org) {
			$db->rollback();
			return $this->bad("要删除的组织机构不存在");
		}
		$name = $org["name"];
		$orgCode = $org["orgCode"];
		
		$rc = $dao->deleteOrg($id);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$log = "删除组织机构： 名称 = {$name} 编码  = {$orgCode}";
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	/**
	 * 新增或编辑用户
	 */
	public function editUser($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		$loginName = $params["loginName"];
		$name = $params["name"];
		$orgCode = $params["orgCode"];
		
		if ($this->isDemo()) {
			if ($id == DemoConst::ADMIN_USER_ID) {
				return $this->bad("在演示环境下，admin用户不希望被您修改，请见谅");
			}
		}
		
		$pys = new PinyinService();
		$py = $pys->toPY($name);
		$params["py"] = $py;
		
		$db = M();
		$db->startTrans();
		
		$dao = new UserDAO($db);
		
		$log = null;
		
		if ($id) {
			// 修改
			
			$rc = $dao->updateUser($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "编辑用户： 登录名 = {$loginName} 姓名 = {$name} 编码 = {$orgCode}";
		} else {
			// 新建
			
			$idGen = new IdGenService($db);
			$id = $idGen->newId();
			$params["id"] = $id;
			
			$rc = $dao->addUser($params);
			if ($rc) {
				$db->rollback();
				return $rc;
			}
			
			$log = "新建用户： 登录名 = {$loginName} 姓名 = {$name} 编码 = {$orgCode}";
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
	 * 删除用户
	 */
	public function deleteUser($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		
		if ($id == DemoConst::ADMIN_USER_ID) {
			return $this->bad("不能删除系统管理员用户");
		}
		
		// 检查用户是否存在，以及是否能删除
		$db = M();
		$db->startTrans();
		
		$dao = new UserDAO($db);
		$user = $dao->getUserById($id);
		
		if (! $user) {
			$db->rollback();
			return $this->bad("要删除的用户不存在");
		}
		$userName = $user["name"];
		$params["name"] = $userName;
		
		$rc = $dao->deleteUser($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$bs = new BizlogService($db);
		$bs->insertBizlog("删除用户[{$userName}]", $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok();
	}

	public function changePassword($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$id = $params["id"];
		
		if ($this->isDemo() && $id == DemoConst::ADMIN_USER_ID) {
			return $this->bad("在演示环境下，admin用户的密码不希望被您修改，请见谅");
		}
		
		$db = M();
		$db->startTrans();
		
		$dao = new UserDAO($db);
		$user = $dao->getUserById($id);
		if (! $user) {
			$db->rollback();
			return $this->bad("要修改密码的用户不存在");
		}
		$loginName = $user["loginName"];
		$name = $user["name"];
		
		$rc = $dao->changePassword($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$log = "修改用户[登录名 ={$loginName} 姓名 = {$name}]的密码";
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, $this->LOG_CATEGORY);
		
		$db->commit();
		
		return $this->ok($id);
	}

	/**
	 * 清除保存登录用户id的session值
	 */
	public function clearLoginUserInSession() {
		session("loginUserId", null);
	}

	/**
	 * 修改“我的密码”
	 */
	public function changeMyPassword($params) {
		if ($this->isNotOnline()) {
			return $this->notOnlineError();
		}
		
		$userId = $params["userId"];
		
		if ($this->isDemo() && $userId == DemoConst::ADMIN_USER_ID) {
			return $this->bad("在演示环境下，admin用户的密码不希望被您修改，请见谅");
		}
		
		if ($userId != $this->getLoginUserId()) {
			return $this->bad("服务器环境发生变化，请重新登录后再操作");
		}
		
		$db = M();
		$db->startTrans();
		
		$dao = new UserDAO($db);
		
		$user = $dao->getUserById($userId);
		if (! $user) {
			return $this->bad("要修改密码的用户不存在");
		}
		$loginName = $user["loginName"];
		$name = $user["name"];
		
		$rc = $dao->changeMyPassword($params);
		if ($rc) {
			$db->rollback();
			return $rc;
		}
		
		$log = "用户[登录名 ={$loginName} 姓名 = {$name}]修改了自己的登录密码";
		$bs = new BizlogService($db);
		$bs->insertBizlog($log, "用户管理");
		
		$db->commit();
		
		return $this->ok();
	}

	public function queryData($queryKey) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$params = array(
				"queryKey" => $queryKey,
				"loginUserId" => $this->getLoginUserId()
		);
		
		$dao = new UserDAO();
		return $dao->queryData($params);
	}

	/**
	 * 判断指定用户id的用户是否存在
	 *
	 * @return true: 存在
	 */
	public function userExists($userId, $db) {
		if (! $db) {
			$db = M();
		}
		if (! $userId) {
			return false;
		}
		
		$sql = "select count(*) as cnt from t_user where id = '%s' ";
		$data = $db->query($sql, $userId);
		return $data[0]["cnt"] == 1;
	}

	/**
	 * 判断指定的组织机构是否存储
	 *
	 * @return boolean true: 存在
	 */
	public function orgExists($orgId, $db) {
		if (! $db) {
			$db = M();
		}
		if (! $orgId) {
			return false;
		}
		
		$sql = "select count(*) as cnt from t_org where id = '%s' ";
		$data = $db->query($sql, $orgId);
		return $data[0]["cnt"] == 1;
	}

	/**
	 * 获得登录用户的数据域
	 */
	public function getLoginUserDataOrg() {
		if ($this->isNotOnline()) {
			return null;
		}
		
		$loginUserId = $this->getLoginUserId();
		$db = M();
		$sql = "select data_org from t_user where id = '%s' ";
		$data = $db->query($sql, $loginUserId);
		if ($data) {
			return $data[0]["data_org"];
		} else {
			return null;
		}
	}

	/**
	 * 获得当前登录用户的某个功能的数据域
	 *
	 * @param unknown $fid        	
	 */
	public function getDataOrgForFId($fid) {
		if ($this->isNotOnline()) {
			return array();
		}
		
		$result = array();
		$loginUserId = $this->getLoginUserId();
		
		if ($loginUserId == DemoConst::ADMIN_USER_ID) {
			// admin 是超级管理员
			$result[] = "*";
			return $result;
		}
		
		$db = M();
		$sql = "select distinct rpd.data_org
				from t_role_permission rp, t_role_permission_dataorg rpd,
					t_role_user ru
				where ru.user_id = '%s' and ru.role_id = rp.role_id
					and rp.role_id = rpd.role_id and rp.permission_id = rpd.permission_id
					and rpd.permission_id = '%s' ";
		$data = $db->query($sql, $loginUserId, $fid);
		
		foreach ( $data as $v ) {
			$result[] = $v["data_org"];
		}
		
		return $result;
	}

	public function orgWithDataOrg() {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$sql = "select id, full_name
				from t_org ";
		
		$queryParams = array();
		$ds = new DataOrgService();
		$rs = $ds->buildSQL("-8999-01", "t_org");
		if ($rs) {
			$sql .= " where " . $rs[0];
			$queryParams = $rs[1];
		}
		
		$sql .= " order by full_name";
		
		$db = M();
		$data = $db->query($sql, $queryParams);
		
		$result = array();
		foreach ( $data as $i => $v ) {
			$result[$i]["id"] = $v["id"];
			$result[$i]["fullName"] = $v["full_name"];
		}
		
		return $result;
	}

	/**
	 * 获得当前登录用户所属公司的Id
	 */
	public function getCompanyId() {
		$params = array(
				"loginUserId" => $this->getLoginUserId()
		);
		
		$dao = new UserDAO();
		
		return $dao->getCompanyId($params);
	}

	/**
	 * 查询用户数据域列表
	 */
	public function queryUserDataOrg($queryKey) {
		if ($this->isNotOnline()) {
			return $this->emptyResult();
		}
		
		$params = array(
				"queryKey" => $queryKey,
				"loginUserId" => $this->getLoginUserId()
		);
		
		$dao = new UserDAO();
		
		return $dao->queryUserDataOrg($params);
	}
}