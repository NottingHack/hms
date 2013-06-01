<?php

	App::uses('ModelBehavior', 'Model');

	App::uses('PhpReader', 'Configure');
	App::uses('Component', 'Controller');
	App::uses('krb5_auth', 'Lib/Krb');

	//! Behaviour that allows a model access to the KrbAuth interface.
	class KrbAuthBehavior extends ModelBehavior
	{
		private $krbObj = null; //<! KrbAuth object.
		private $useDummy = false; //!< If true, don't actually use KrbAuth object and return true for all queries.

		//! Perform initial setup.
		/*!
			@param Model $model The model we're being attached to.
			@param array $settings Any settings passed from the model.
			@sa http://book.cakephp.org/2.0/en/models/behaviors.html#creating-a-behavior-callback
		*/
		public function setup(Model $model, $settings = array())
		{
			Configure::config('default', new PhpReader());
			Configure::load('krb', 'default');

			$this->useDummy = Configure::read('krb_useDummy');
			if(	isset($this->useDummy) == false ||
				$this->useDummy == false)
			{
				$this->krbObj = new krb5_auth(Configure::read('krb_username'), Configure::read('krb_tab'), Configure::read('krb_relm'));
			}
		}

		//! Check to see if the password is correct.
		/*!
			@param Model $model The model we're attached to.
			@param string $username The username to check.
			@param string $password The password to check.
			@retval bool True if password is correct (or useDummy is true), false otherwise.
		*/
		public function krbCheckPassword(Model $model, $username, $password)
		{
			if($this->useDummy)
			{
				return true;
			}

			return $this->krbObj->check_password($username, $password);
		}

		//! Add a new user to the KrbAuth system.
		/*!
			@param Model $model The model we're attached to.
			@param string $username The username to create.
			@param string $password The password to create.
			@retval bool True if creation succeeded (or useDummy is true), false otherwise.
		*/
		public function krbAddUser(Model $model, $username, $password)
		{
			if($this->useDummy)
			{
				return true;
			}

			return $this->krbObj->add_user($username, $password);
		}

		//! Delete an existing user from the KrbAuth system.
		/*!
			@param Model $model The model we're attached to.
			@param string $username The username to delete.
			@retval bool True if deletion succeeded (or useDummy is true), false otherwise.
		*/
		public function krbDeleteUser(Model $model, $username)
		{
			if($this->useDummy)
			{
				return true;
			}

			return $this->krbObj->delete_user($username);
		}

		//! Update the users password.
		/*!
			@param Model $model The model we're attached to.
			@param string $username The username to update.
			@param string $newPass The new password to use.
			@retval bool True if password update succeeded (or useDummy is true), false otherwise.
		*/
		public function krbChangePassword(Model $model, $username, $newPass)
		{
			if($this->useDummy)
			{
				return true;
			}
			
			return $this->krbObj->change_password($username, $newPass);
		}

		//! Detect if a user exists.
		/*!
			@param Model $model The model we're attached to.
			@param string $username The username to check.
			@retval bool True if user exists (or useDummy is true), false otherwise.
		*/
		public function krbUserExists(Model $model, $username)
		{
			if($this->useDummy)
			{
				return true;
			}

			return $this->krbObj->user_exists($username);
		}
	}

?>