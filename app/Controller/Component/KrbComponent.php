<?php

App::uses('Component', 'Controller');

Configure::config('default', new PhpReader());
Configure::load('krb', 'default');

App::uses('krb5_auth', 'Lib/Krb');

class KrbComponent extends Component {

	var $krbObj = null;
	var $use_dummy = false;

	public function __construct(ComponentCollection $collection, $settings = array())
	{
		$this->use_dummy = Configure::read('krb_use_dummy');
		if(	isset($this->use_dummy) == false ||
			$this->use_dummy == false)
		{
			$this->krbObj = new krb5_auth(Configure::read('krb_username'), Configure::read('krb_tab'), Configure::read('krb_relm'));
		}
	}

	public function checkPassword($username, $password)
	{
		if($this->use_dummy)
		{
			return true;
		}

		return $this->krbObj->check_password($username, $password);
	}

	public function addUser($username, $password)
	{
		if($this->use_dummy)
		{
			return true;
		}

		return $this->krbObj->add_user($username, $password);
	}

	public function deleteUser($username)
	{
		if($this->use_dummy)
		{
			return true;
		}

		return $this->krbObj->delete_user($username);
	}

	public function changePassword($username, $newPass)
	{
		if($this->use_dummy)
		{
			return true;
		}
		
		return $this->krbObj->change_password($username, $newPass);
	}
}

?>