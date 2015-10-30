<?php

namespace Home\Controller;

use Think\Controller;
use Home\Service\UserService;
use Home\Common\FIdConst;
use Home\Service\BizConfigService;

/**
 * 用户管理Controller
 *
 * @author 李静波
 *        
 */
class UserController extends Controller {

	/**
	 * 用户管理-主页面
	 */
	public function index() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::USR_MANAGEMENT)) {
			$bcs = new BizConfigService();
			$this->assign("productionName", $bcs->getProductionName());
			
			$this->assign("loginUserName", $us->getLoignUserNameWithOrgFullName());
			$this->assign("title", "用户管理");
			$this->assign("uri", __ROOT__ . "/");
			$dtFlag = getdate();
			$this->assign("dtFlag", $dtFlag[0]);
			
			$this->display();
		} else {
			redirect(__ROOT__ . "/");
		}
	}

	/**
	 * 登录页面
	 */
	public function login() {
		if (session("loginUserId")) {
			redirect(__ROOT__);
		}
		
		$bcs = new BizConfigService();
		$this->assign("productionName", $bcs->getProductionName());
		$this->assign("title", "登录");
		$this->assign("uri", __ROOT__ . "/");
		$dtFlag = getdate();
		$this->assign("dtFlag", $dtFlag[0]);
		$us = new UserService();
		$this->assign("demoInfo", $us->getDemoLoginInfo());
		
		$this->display();
	}

	/**
	 * 页面：修改我的密码
	 */
	public function changeMyPassword() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::CHANGE_MY_PASSWORD)) {
			$bcs = new BizConfigService();
			$this->assign("productionName", $bcs->getProductionName());
			$this->assign("loginUserId", $us->getLoginUserId());
			$this->assign("loginName", $us->getLoginName());
			$this->assign("loginUserName", $us->getLoignUserNameWithOrgFullName());
			$this->assign("title", "修改我的密码");
			$this->assign("uri", __ROOT__ . "/");
			$dtFlag = getdate();
			$this->assign("dtFlag", $dtFlag[0]);
			$this->display();
		} else {
			redirect(__ROOT__ . "/");
		}
	}

	/**
	 * 修改我的密码，POST方法
	 */
	public function changeMyPasswordPOST() {
		if (IS_POST) {
			$us = new UserService();
			$params = array(
					"userId" => I("post.userId"),
					"oldPassword" => I("post.oldPassword"),
					"newPassword" => I("post.newPassword")
			);
			
			$result = $us->changeMyPassword($params);
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 用户登录，POST方法
	 */
	public function loginPOST() {
		if (IS_POST) {
			$loginName = I("post.loginName");
			$password = I("post.password");
			$fromDevice = I("post.fromDevice");
			$us = new UserService();
			$this->ajaxReturn($us->doLogin($loginName, $password, $fromDevice));
		}
	}

	/**
	 * 获得组织机构树
	 */
	public function allOrgs() {
		$us = new UserService();
		$data = $us->allOrgs();
		
		$this->ajaxReturn($data);
	}

	/**
	 * 获得组织机构下的用户列表
	 */
	public function users() {
		if (IS_POST) {
			$us = new UserService();
			$params = array(
					"orgId" => I("post.orgId"),
					"start" => I("post.start"),
					"limit" => I("post.limit")
			);
			
			$this->ajaxReturn($us->users($params));
		}
	}

	/**
	 * 新建或编辑组织结构
	 */
	public function editOrg() {
		if (IS_POST) {
			$us = new UserService();
			$id = I("post.id");
			$name = I("post.name");
			$parentId = I("post.parentId");
			$orgCode = I("post.orgCode");
			
			$result = $us->editOrg($id, $name, $parentId, $orgCode);
			
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 获得组织机构的名称
	 */
	public function orgParentName() {
		if (IS_POST) {
			$us = new UserService();
			$id = I("post.id");
			$data = $us->orgParentName($id);
			
			$this->ajaxReturn($data);
		}
	}

	/**
	 * 删除组织机构
	 */
	public function deleteOrg() {
		if (IS_POST) {
			$us = new UserService();
			$id = I("post.id");
			$data = $us->deleteOrg($id);
			
			$this->ajaxReturn($data);
		}
	}

	/**
	 * 新增或编辑用户
	 */
	public function editUser() {
		if (IS_POST) {
			$us = new UserService();
			
			$params = array(
					"id" => I("post.id"),
					"loginName" => I("post.loginName"),
					"name" => I("post.name"),
					"orgCode" => I("post.orgCode"),
					"orgId" => I("post.orgId"),
					"enabled" => I("post.enabled") == "true" ? 1 : 0,
					"gender" => I("post.gender"),
					"birthday" => I("post.birthday"),
					"idCardNumber" => I("post.idCardNumber"),
					"tel" => I("post.tel"),
					"tel02" => I("post.tel02"),
					"address" => I("post.address")
			);
			
			$result = $us->editUser($params);
			
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 删除用户
	 */
	public function deleteUser() {
		if (IS_POST) {
			$us = new UserService();
			
			$params = array(
					"id" => I("post.id")
			);
			
			$result = $us->deleteUser($params);
			
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 修改用户的密码
	 */
	public function changePassword() {
		if (IS_POST) {
			$us = new UserService();
			
			$params = array(
					"id" => I("post.id"),
					"password" => I("post.password")
			);
			
			$result = $us->changePassword($params);
			
			$this->ajaxReturn($result);
		}
	}

	/**
	 * 用户自定义字段，查询数据
	 */
	public function queryData() {
		if (IS_POST) {
			$queryKey = I("post.queryKey");
			$us = new UserService();
			$this->ajaxReturn($us->queryData($queryKey));
		}
	}
}
