<?php

require_once ('HmsContext.php');

class MemberContext extends HmsContext {

	const STATUS_PROSPECTIVE_MEMBER = 1;
	const STATUS_PRE_MEMBER_1 = 2;
	const STATUS_PRE_MEMBER_2 = 3;
	const STATUS_PRE_MEMBER_3 = 4;
	const STATUS_CURRENT_MEMBER = 5;
	const STATUS_EX_MEMBER = 6;

	private $__testMemberData = array();

	public function getNewEmail() {
		return time() . '@example.com';
	}

	public function getNewUsername() {
		return 'testUsername' . time();
	}

	public function setTestMemberData($key, $value) {
		$this->__testMemberData[$key] = $value;
	}

	public function getTestMemberData($key) {
		return $this->__testMemberData[$key];
	}

}