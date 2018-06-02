<?php

namespace Home\DAO;

/**
 * ���� DAO
 *
 * @author ���
 */
class PSIBaseExDAO extends PSIBaseDAO {
	/**
	 *
	 * @var \Think\Model $db
	 */
	protected $db;

	function __construct($db) {
		$this->db = $db;
	}

	/**
	 * ����ȫ��ΨһId ��UUID��
	 *
	 * @return string
	 */
	public function newId() {
		$db = $this->db;
		
		$data = $db->query("select UUID() as uuid");
		
		return strtoupper($data[0]["uuid"]);
	}

	protected function loginUserIdNotExists($loginUserId) {
		$db = $this->db;
		
		$sql = "select count(*) as cnt from t_user where id = '%s' ";
		$data = $db->query($sql, $loginUserId);
		$cnt = $data[0]["cnt"];
		
		return $cnt != 1;
	}

	protected function dataOrgNotExists($dataOrg) {
		$db = $this->db;
		
		$sql = "select count(*) as cnt from t_user where data_org = '%s' ";
		$data = $db->query($sql, $dataOrg);
		$cnt = $data[0]["cnt"];
		
		return $cnt != 1;
	}

	protected function companyIdNotExists($companyId) {
		$db = $this->db;
		
		$sql = "select count(*) as cnt from t_org where id = '%s' ";
		$data = $db->query($sql, $companyId);
		$cnt = $data[0]["cnt"];
		
		return $cnt != 1;
	}

	/**
	 * �ж������Ƿ�����ȷ��Y-m-d��ʽ
	 *
	 * @param string $date        	
	 * @return boolean true: ����ȷ�ĸ�ʽ
	 */
	protected function dateIsValid($date) {
		$dt = strtotime($date);
		if (! $dt) {
			return false;
		}
		
		return date("Y-m-d", $dt) == $date;
	}

	/**
	 * �ս��
	 *
	 * @return array
	 */
	protected function emptyResult() {
		return [];
	}

	/**
	 * ��������
	 *
	 * @param string $param
	 *        	��������
	 * @return array
	 */
	protected function badParam($param) {
		return $this->bad("����" . $param . "����ȷ");
	}

	/**
	 * �������ַ���ǰ��Ŀո�ȥ�����ж��Ƿ��ǿ��ַ���
	 *
	 * @param string $s        	
	 *
	 * @return true: ���ַ���
	 */
	protected function isEmptyStringAfterTrim($s) {
		$result = trim($s);
		return $result == null || $result == "";
	}

	/**
	 * �ж��ַ��������Ƿ񳬹��޶�
	 *
	 * @param string $s        	
	 * @param int $length
	 *        	Ĭ�ϳ��Ȳ��ܳ���255
	 * @return bool true���������޶�
	 */
	protected function stringBeyondLimit(string $s, int $length = 255): bool {
		return strlen($s) > $length;
	}
}