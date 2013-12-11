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
 * @package       app.Lib.Krb
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Dummy KrbAuth implementation for development.
 */
class Krb5Auth {

/**
 * Constructor.
 * @param string $krbUsername Not used.
 * @param string $keytab Not used.
 * @param string $realm Not used.
 */
	public function __construct($krbUsername, $keytab, $realm) {
	}

/**
 * Add a new user.
 * @param string $username The username of the new user.
 * @param string $password The password for the user.
 * @return bool True.
 */
	public function addUser($username, $password) {
		return true;
	}

/**
 * Remove a user.
 * @param string $username The username of the user to remove.
 * @return bool True.
 */
	public function deleteUser($username) {
		return true;
	}

/**
 * Check if a username and password matches a known record.
 * @param  string $username The username to check.
 * @param  striing $password The password to check.
 * @return bool True.
 */
	public function checkPassword($username, $password) {
		return true;
	}

/**
 * Change the password for a user.
 * @param  string $username The users username.
 * @param  string $newpassword The new password.
 * @return bool True.
 */
	public function changePassword($username, $newpassword) {
		return true;
	}

/**
 * Check if we know of a user with a certain username.
 * @param  string $username The username to check.
 * @return bool True.
 */
	public function userExists($username) {
		return true;
	}
}