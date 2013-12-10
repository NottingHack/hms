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
 * @package       app.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Controller for the EmailRecords functionality, allows authorized users
 * to view the emails that have been sent to a member.
 */
class EmailRecordsController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/** 
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		Controller::loadModel('Member');

		if (parent::isAuthorized($user, $request)) {
			return true;
		}

		$memberId = $this->Member->getIdForMember($user);
		$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_ADMIN );
		$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_TEAM );
		$actionHasParams = isset( $request->params ) && isset($request->params['pass']) && count( $request->params['pass'] ) > 0;
		$memberIdIsSet = is_numeric($memberId);

		$firstParamIsMemberId = ( $actionHasParams && $memberIdIsSet && $request->params['pass'][0] == $memberId );

		switch ($request->action) {
			case 'index':
				return ($memberIsMembershipAdmin || $memberIsOnMembershipTeam);

			case 'view':
				return ($memberIsMembershipAdmin || $memberIsOnMembershipTeam || $firstParamIsMemberId);
		}

		return false;
	}

/**
 * Given a list of e-mails, set the memberNames and emails vars
 * on the view.
 * @param  array $emails Array of e-mail record data.
 */
	private function __setupEmailListView($emails) {
		Controller::loadModel('Member');
		$this->set('memberNames', $this->Member->getBestMemberNames());
		$this->set('emails', $emails);
	}

/**
 * Display a list of all e-mails for all members.
 */
	public function index() {
		$this->__setupEmailListView($this->EmailRecord->getAllEmails());
	}

/**
 * Display a list of all e-mails for a specific member.
 * @param  int|null $id Show all e-mails sent to this member.
 */
	public function view($id = null) {
		$viewerId = $this->_getLoggedInMemberId();

		$memberHasFullAccess = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::FULL_ACCESS);
		$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_ADMIN);
		$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_TEAM);

		$canView =
			is_numeric($id) &&
			( $memberHasFullAccess || $memberIsMembershipAdmin || $memberIsOnMembershipTeam || $id == $viewerId );

		if ($canView) {
			$this->__setupEmailListView($this->EmailRecord->getAllEmailsForMember($id));
			$this->set('id', $id);
		} else {
			return $this->redirect($this->referer());
		}
	}
}