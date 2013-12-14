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
 * Real KrbAuth implementation for production.
 */
class Krb5Auth {

/**
 * The KADM5 connection to use.
 * @var KADM5.
 */
	private $__krbConn;

/**
 * The relm to use.
 * @var string
 */
	private $__realm;

/**
 * If true, we're in debug mode and shouldn't actually take any action.
 * @var bool
 */
	private $__debug;

/**
 * Constructor.
 * @param string $krbUsername Username to construct the KADM5 object with.
 * @param string $keytab Keytam to construct the KADM5 object with.
 * @param string $realm Relm we belong to.
 */
	public function __construct($krbUsername, $keytab, $realm) {
		$this->__debug = false;
		$this->__realm = $realm;
		$this->__krbConn = new KADM5($krbUsername, $keytab, true); // use keytab=true
	}

/**
 * Add a new user.
 * @param string $username The username of the new user.
 * @param string $password The password for the user.
 * @return bool True if sucessful, false otherwise.
 */
	public function addUser($username, $password) {
		/* Just incase some smartarse appends /admin to their handle
		* in an attempt to become a krb admin... */
		if (stristr($username, '/admin') === false) {
			try {
				$princ = new KADM5Principal(strtolower($username));
				$this->__krbConn->createPrincipal($princ, $password);
			} catch (Exception $e) {
				if ($this->__debug) {
					echo "$e\n";
				}
				return false;
			}
			return true;
		} else {
			if ($this->__debug) {
				echo "Attempt to create admin user stopped.";
			}
			return false;
		}
	}

/**
 * Remove a user.
 * @param string $username The username of the user to remove.
 * @return bool True if sucessful, false otherwise.
 */
	public function deleteUser($username) {
		try {
			$princ = $this->__krbConn->getPrincipal(strtolower($username));
			$princ->delete();
		} catch (Exception $e) {
			if ($this->__debug) {
				echo "$e\n";
			}
			return false;
		}
		return true;
	}

/**
 * Check if a username and password matches a known record.
 * @param  string $username The username to check.
 * @param  striing $password The password to check.
 * @return bool True if username and password match a record, false otherwise.
 */
	public function checkPassword($username, $password) {
		$ticket = new KRB5CCache();
		try {
			$ticket->initPassword(strtolower($username) . "@" . $this->__realm, $password);
		} catch (Exception $e) {
			if ($this->__debug) {
				echo "$e\n";
			}
			return false;
		}
		return true;
	}

/**
 * Change the password for a user.
 * @param  string $username The users username.
 * @param  string $newpassword The new password.
 * @return bool True if sucessful, false otherwise.
 */
	public function changePassword($username, $newpassword) {
		try {
			$princ = $this->__krbConn->getPrincipal(strtolower($username));
			$princ->changePassword($newpassword);
		} catch (Exception $e) {
			if ($this->__debug) {
				echo "$e\n";
			}
			return false;
		}
		return true;
	}

/**
 * Check if we know of a user with a certain username.
 * @param  string $username The username to check.
 * @return bool|null True if user exists, false if user does not exist. Null on error.
 */
	public function userExists($username) {
		try {
			$this->__krbConn->getPrincipal(strtolower($username));
		} catch (Exception $e) {
			if ($e->getMessage() == "Principal does not exist") {
				return false;
			} else {
				return null;
			}
		}
		return true;
	}
}