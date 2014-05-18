<?php

require_once ('HmsContext.php');

class DatabaseContext extends HmsContext {

	private function __getConnection() {
		$databaseConfig = $this->_configContext()->readDatabaseConfig();
		$mysqli = new mysqli($databaseConfig['host'], $databaseConfig['login'], $databaseConfig['password'], $databaseConfig['database']);

		if ($mysqli->connect_errno) {
			$this->_fail('Failed to connect to MySQL: (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}

		return $mysqli;
	}

	private function __runQuery($mysqli, $query) {
		$result = $mysqli->query($query);
		if (!$result) {
			$this->_fail('Failed to run query ' . $query . ' (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$rows = array();
		while ($row = $result->fetch_assoc()) {
			array_push($rows, $row);
		}
		return $rows;
	}

	private function __getDataForMemberWithStatus($status) {
		$mysqli = $this->__getConnection();
		$query = "SELECT * FROM members WHERE member_status=$status LIMIT 1";
		$data = $this->__runQuery($mysqli, $query);
		return $data[0];
	}

	public function getMemberAdminEmailAddresses() {
		$mysqli = $this->__getConnection();
		$query = 'SELECT members.email FROM members INNER JOIN member_group ON members.member_id=member_group.member_id WHERE member_group.grp_id=5';
		$emails = $this->__runQuery($mysqli, $query);
		return array_map(function($element) {
			return $element['email'];
		}, $emails);
	}

	public function getEmailForMemberWithStatus($status) {
		$memberData = $this->__getDataForMemberWithStatus($status);
		return $memberData['email'];
	}

	public function getIdAndEmailForMemberWithStatus($status) {
		$memberData = $this->__getDataForMemberWithStatus($status);
		return array(
			'id' => $memberData['member_id'],
			'email' => $memberData['email']
		);
	}

}