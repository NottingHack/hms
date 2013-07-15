<?php
App::uses('HmsAuthenticate', 'Controller/Component/Auth');
App::uses('Member', 'Model');

class MemberVoiceAppController extends AppController {

	/*
	This function is the only function that needs to be aware of the outside app.
	Returns app defined user id
	*/
	protected function _getUserID() {
		return $this->Member->getIdForMember($this->Auth->user());
	}
}
?>