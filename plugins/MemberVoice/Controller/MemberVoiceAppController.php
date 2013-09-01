<?php
App::uses('HmsAuthenticate', 'Controller/Component/Auth');
App::uses('Member', 'Model');

/**
 * Functions and properties for all controllers in the plugin
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MemberVoiceAppController extends AppController {

	// We need to access the firstname and lastname of the user model of the app
	public $mvFirstName = 'firstname';
	public $mvLastName = 'surname';

	//! Returns the userID of the currently logged in user
	/*!
		@retval mixed The userID as defined by the containing application
		This function is the only function that needs to be aware of the outside app.
	*/
	protected function _getUserID() {
		return $this->_getLoggedInMemberId();
	}

	public function isAuthorized($user, $request) {
		if(parent::isAuthorized($user, $request)) {
			return true;
		}

		$memberId = $this->Member->getIdForMember($user);

		if ($memberId > 0) {
			return true;
		}

		return false;
	}
}
?>