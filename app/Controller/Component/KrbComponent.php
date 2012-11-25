<?php

App::uses('Component', 'Controller');

Configure::config('default', new PhpReader());
Configure::load('krb', 'default');

App::uses('krb5_auth', 'Lib/Krb');

class KrbComponent extends Component {

	var $krbObj = null;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->krbObj = new krb5_auth(Configure::read('krb_username'), Configure::read('krb_tab'), Configure::read('krb_relm'));
	}

	public function checkPassword($username, $password)
	{
		return $this->krbObj->check_password($username, $password);
	}

	public function addUser($username, $password)
	{
		return $this->krbObj->add_user($username, $password);
	}

	public function deleteUser($username)
	{
		return $this->krbObj->delete_user($username);
	}

	public function changePassword($username, $newPass)
	{
		return $this->krbObj->change_password($username, $newPass);
	}
}

?>