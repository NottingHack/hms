<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ModelBehavior', 'Model');

App::uses('PhpReader', 'Configure');
App::uses('Component', 'Controller');
App::uses('Krb5Auth', 'Lib/Krb');

/**
 * Behaviour that allows a model access to the KrbAuth interface.
 */
class KrbAuthBehavior extends ModelBehavior {

/**
 * KrbAuth object.
 * @var KrbAuth
 */
	private $__krbObj = null;

/**
 * Perform initial setup.
 * 
 * @param Model $model The model we're being attached to.
 * @param array $settings Any settings passed from the model.
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#creating-a-behavior-callback
 */
	public function setup(Model $model, $settings = array()) {
		Configure::config('default', new PhpReader());
		Configure::load('krb', 'default');

		$this->__krbObj = new Krb5Auth(Configure::read('krb_username'), Configure::read('krb_tab'), Configure::read('krb_relm'));
	}

/**
 * Check to see if the password is correct.
 * 
 * @param Model $model The model we're attached to.
 * @param string $username The username to check.
 * @param string $password The password to check.
 * @return bool True if password is correct (or useDummy is true), false otherwise.
 */
	public function krbCheckPassword(Model $model, $username, $password) {
		return $this->__krbObj->checkPassword($username, $password);
	}

/**
 * Add a new user to the KrbAuth system.
 * 
 * @param Model $model The model we're attached to.
 * @param string $username The username to create.
 * @param string $password The password to create.
 * @return bool True if creation succeeded (or useDummy is true), false otherwise.
 */
	public function krbAddUser(Model $model, $username, $password) {
		return $this->__krbObj->addUser($username, $password);
	}

/**
 * Delete an existing user from the KrbAuth system.
 * 
 * @param Model $model The model we're attached to.
 * @param string $username The username to delete.
 * @return bool True if deletion succeeded (or useDummy is true), false otherwise.
 */
	public function krbDeleteUser(Model $model, $username) {
		return $this->__krbObj->deleteUser($username);
	}

/**
 * Update the users password.
 * 
 * @param Model $model The model we're attached to.
 * @param string $username The username to update.
 * @param string $newPass The new password to use.
 * @return bool True if password update succeeded (or useDummy is true), false otherwise.
 */
	public function krbChangePassword(Model $model, $username, $newPass) {
		return $this->__krbObj->changePassword($username, $newPass);
	}

/**
 * Detect if a user exists.
 * 
 * @param Model $model The model we're attached to.
 * @param string $username The username to check.
 * @return bool True if user exists (or useDummy is true), false otherwise.
 */
	public function krbUserExists(Model $model, $username) {
		return $this->__krbObj->userExists($username);
	}
}