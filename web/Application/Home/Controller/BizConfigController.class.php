<?php

namespace Home\Controller;

use Think\Controller;
use Home\Service\UserService;
use Home\Service\BizConfigService;
use Home\Common\FIdConst;

class BizConfigController extends Controller {

	public function index() {
		$us = new UserService();

		$this->assign("title", "业务设置");
		$this->assign("uri", __ROOT__ . "/");

		$this->assign("loginUserName", $us->getLoginUserName());
		
		$dtFlag = getdate();
		$this->assign("dtFlag", $dtFlag[0]);

		if ($us->hasPermission(FIdConst::BIZ_CONFIG)) {
			$this->display();
		} else {
			redirect(__ROOT__ . "/Home/User/login");
		}
	}

	public function allConfigs() {
		if (IS_POST) {
			$bs = new BizConfigService();
			
			$this->ajaxReturn($bs->allConfigs());
		}
	}
	
	public function edit() {
		if (IS_POST) {
			$bs = new BizConfigService();
			
			$params = array();
			
			$this->ajaxReturn($bs->edit($params));
		}
	}
}