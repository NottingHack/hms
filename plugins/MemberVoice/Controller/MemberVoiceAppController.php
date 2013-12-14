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
 * @package       plugins.MemberVoice.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('HmsAuthenticate', 'Controller/Component/Auth');
App::uses('Member', 'Model');

/**
 * Base controller for all controllers in the MemberVoice plugin.
 */
class MemberVoiceAppController extends AppController {

/**
 * The key in the user data that represents the firstname.
 * @var string
 */
	protected $_mvFirstName = 'firstname';

/**
 * The key in the user data that represens the surname.
 * @var string
 */
	protected $_mvLastName = 'surname';

/**
 * Returns the userID of the currently logged in user
 * 
 * @return mixed The userID as defined by the containing application
 */
	protected function _getUserID() {
		return $this->_getLoggedInMemberId();
	}

/**
 * Check to see if a user is authorized to perform an action.
 * @param  array $user Array of user data
 * @param  CakeRequest $request The request the user is attempting to perform.
 * @return bool True if the user is authorized to perform the action, false otherwise.
 */
	public function isAuthorized($user, $request) {
		if (parent::isAuthorized($user, $request)) {
			return true;
		}

		$memberId = $this->Member->getIdForMember($user);

		if ($memberId > 0) {
			return true;
		}

		return false;
	}
}