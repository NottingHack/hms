<?php

require_once ('HmsContext.php');

class MemberContext extends HmsContext {

	private $__testMemberData = array();

	public function getNewEmail() {
		return time() . '@example.com';
	}

	public function setTestMemberData($key, $value) {
		$this->__testMemberData[$key] = $value;
	}

	public function getTestMemberData($key) {
		return $this->__testMemberData[$key];
	}

}