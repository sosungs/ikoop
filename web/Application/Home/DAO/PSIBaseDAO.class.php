<?php

namespace Home\DAO;

/**
 * PSI DAO ����
 *
 * @author ���
 */
class PSIBaseDAO {

	/**
	 * ����ʧ��
	 *
	 * @param string $msg
	 *        	������Ϣ
	 * @return array
	 */
	protected function bad($msg) {
		return array(
				"success" => false,
				"msg" => $msg
		);
	}

	/**
	 * ���ݿ����
	 *
	 * @param string $methodName
	 *        	��������
	 * @param int $codeLine
	 *        	�����к�
	 * @return array
	 */
	protected function sqlError($methodName, $codeLine) {
		$info = "���ݿ��������ϵ����Ա<br />����λ��{$methodName} - {$codeLine}��";
		return $this->bad($info);
	}

	/**
	 * ��ʱ�����͸�ʽ��������2015-08-13�ĸ�ʽ
	 *
	 * @param string $d        	
	 * @return string
	 */
	protected function toYMD($d) {
		return date("Y-m-d", strtotime($d));
	}

	/**
	 * ��ǰ���ܻ�û�п���
	 *
	 * @param string $info
	 *        	������Ϣ
	 * @return array
	 */
	protected function todo($info = null) {
		if ($info) {
			return array(
					"success" => false,
					"msg" => "TODO: ���ܻ�û����, ������Ϣ��$info"
			);
		} else {
			return array(
					"success" => false,
					"msg" => "TODO: ���ܻ�û����"
			);
		}
	}
}