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

/**
 * Behaviour that allows a model access to the MockKrbAuth interface.
 */
class MockKrbAuthBehavior extends ModelBehavior {

/**
 * Perform initial setup.
 *
 * @param Model $model The model we're being attached to.
 * @param array $settings Any settings passed from the model.
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#creating-a-behavior-callback
 */
	public function setup(Model $model, $settings = array()) {
	}

/**
 * Check to see if the password is correct.
 *
 * @param Model $model The model we're attached to.
 * @param string $username The username to check.
 * @param string $password The password to check.
 * @return bool True.
 */
	public function krbCheckPassword(Model $model, $username, $password) {
		return true;
	}

/**
 * Add a new user to the KrbAuth system.
 *
 * @param Model $model The model we're attached to.
 * @param string $username The username to create.
 * @param string $password The password to create.
 * @return bool True.
 */
	public function krbAddUser(Model $model, $username, $password) {
		return true;
	}

/**
 * Delete an existing user from the KrbAuth system.
 *
 * @param Model $model The model we're attached to.
 * @param string $username The username to delete.
 * @return bool True.
 */
	public function krbDeleteUser(Model $model, $username) {
		return true;
	}

/**
 * Update the users password.
 *
 * @param Model $model The model we're attached to.
 * @param string $username The username to update.
 * @param string $newPass The new password to use.
 * @return bool True.
 */
	public function krbChangePassword(Model $model, $username, $newPass) {
		return true;
	}

/**
 * Detect if a user exists.
 *
 * @param Model $model The model we're attached to.
 * @param string $username The username to check.
 * @return bool True.
 */
	public function krbUserExists(Model $model, $username) {
		return true;
	}
}