<?php

namespace Home\Service;

/**
 * Service 扩展基类
 *
 * @author 李静波
 */
class PSIBaseExService extends PSIBaseService {
	private $userService = null;

	private function us() {
		if (! $this->userService) {
			$this->userService = new UserService();
		}
		
		return $this->userService;
	}

	/**
	 * 当前登录用户的id
	 */
	protected function getLoginUserId() {
		$us = $this->us();
		return $us->getLoginUserId();
	}

	/**
	 * 当前登录用户的数据域
	 */
	protected function getLoginUserDataOrg() {
		$us = $this->us();
		return $us->getLoginUserDataOrg();
	}

	/**
	 * 当前登录用户所属公司的id
	 */
	protected function getCompanyId() {
		$us = $this->us();
		return $us->getCompanyId();
	}
}