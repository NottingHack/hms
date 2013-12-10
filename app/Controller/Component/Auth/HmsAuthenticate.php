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
 * @package       app.Controller.Component.Auth
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('FormAuthenticate', 'Controller/Component/Auth');

/**
 * HMS Authenticate.
 *
 * A specialisation of CakePHP's FormAuthienticate conponent
 * that allows users to login with either a username or an e-mail
 * and checking those auth details against records on a KRB server.
 *
 * @package app.Controller.Component.Auth
 * 
 */
class HmsAuthenticate extends FormAuthenticate {

/**
 * Check if the details provided by a user are valid
 * @param  CakeRequest  $request The information the user is submitting
 * @param  CakeResponse $response Not-used
 * @return bool True if user-supplied values are valid, false otherwise.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		// Need to use the members model
		$memberModel = ClassRegistry::init("Member");

		// Find the member
		// Try username first
		$memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.username' => $request->data['User']['usernameOrEmail'] ) ) );

		if (isset($memberInfo) && $memberInfo != null) {
			return $memberModel->krbCheckPassword($request->data['User']['usernameOrEmail'], $request->data['User']['password']) ? $memberInfo : false;
		}

		// Fall-back to the e-mail
		$memberInfo = $memberModel->find('first', array( 'conditions' => array( 'Member.email' => $request->data['User']['usernameOrEmail'] ) ) );
		if (isset($memberInfo) && $memberInfo != null) {
			return $memberModel->krbCheckPassword($memberInfo['Member']['username'], $request->data['User']['password']) ? $memberInfo : false;
		}

		// Couldn't find either the username or password
		return false;
	}
}