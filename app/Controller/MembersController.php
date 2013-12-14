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
App::uses('HmsAuthenticate', 'Controller/Component/Auth');
App::uses('Member', 'Model');
App::uses('ForgotPassword', 'Model');
App::uses('CakeEmail', 'Network/Email');
App::uses('PhpReader', 'Configure');
Configure::config('default', new PhpReader());
Configure::load('hms', 'default');

/**
 * Controller to handle Member functionality, allows members to be viewed,
 * edited, and registered.
 */
class MembersController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form', 'Paginator', 'Tinymce', 'Currency', 'Mailinglist');

/**
 * The list of components this Controller relies on.
 * @var array
 */
	public $components = array('BankStatement');

/** 
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
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
			case 'revokeMembership':
			case 'reinstateMembership':
			case 'acceptDetails':
			case 'rejectDetails':
			case 'addExistingMember':
			case 'uploadCsv':
			case 'emailMembersWithStatus':
				return $memberIsMembershipAdmin;

			case 'sendMembershipReminder':
			case 'sendContactDetailsReminder':
			case 'sendSoDetailsReminder':
			case 'approveMember':
			case 'index':
			case 'listMembers':
			case 'listMembersWithStatus':
			case 'search':
				return $memberIsMembershipAdmin || $memberIsOnMembershipTeam;

			case 'changePassword':
			case 'edit':
				return $memberIsMembershipAdmin || $firstParamIsMemberId;

			case 'view':
				return $memberIsMembershipAdmin || $memberIsOnMembershipTeam || $firstParamIsMemberId;

			case 'setupDetails':
				return $firstParamIsMemberId;
		}

		return false;
	}

/**
 * Perform any actions that should be performed before any controller action.
 *
 * @link http://api20.cakephp.org/class/controller#method-ControllerbeforeFilter
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$allowedActionsArray = array(
			'logout',
			'login',
			'forgotPassword',
			'setupLogin',
			'setupDetails'
		);

		$memberId = $this->_getLoggedInMemberId();
		$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_ADMIN );
		$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_TEAM );
		$isLocal = $this->isRequestLocal();

		// We have to put register here, as is isAuthorized()
		// cannot be used to check access to functions if they can
		// ever be accessed by a user that is not logged in
		if ( $isLocal || ( $memberIsMembershipAdmin || $memberIsOnMembershipTeam ) ) {
			array_push($allowedActionsArray, 'register');
		}

		$this->Auth->allow($allowedActionsArray);
	}

/**
 * Show a list of all Status and a count of how many members are in each status.
 */
	public function index() {
		$this->set('memberStatusInfo', $this->Member->Status->getStatusSummaryAll());
		$this->set('memberTotalCount', $this->Member->getCount());

		$this->Nav->add('Register Member', 'members', 'register');
		$this->Nav->add('E-mail all current members', 'members', 'emailMembersWithStatus', array( Status::CURRENT_MEMBER ) );
		$this->Nav->add('Upload CSV', 'members', 'uploadCsv' );
	}
/**
 * Show a list of all members, their e-mail address, status and the groups they're in.
 */
	public function listMembers() {
		/*
			Actions should be added to the array like so:
				[actions] =>
						[n]
							[title] => action title
							[controller] => action controller
							[action] => action name
							[params] => array of params
		*/
		$this->__paginateMemberList($this->Member->getMemberSummaryAll(true));
	}

/**
 * List all members with a particular status.
 *
 * @param int $statusId The status to list all members for.
 */
	public function listMembersWithStatus($statusId) {
		// Use the list members view
		$this->view = 'list_members';

		// If statusId is not set, list all the members
		if (!isset($statusId)) {
			return $this->redirect( array('controller' => 'members', 'action' => 'listMembers') );
		}

		$this->__paginateMemberList($this->Member->getMemberSummaryForStatus(true, $statusId));
		$this->set('statusInfo', $this->Member->Status->getStatusSummaryForId($statusId));
	}
/**
 * List all members who's name, email, username or handle is similar to the search term.
 */
	public function search() {
		// Use the list members view
		$this->view = 'list_members';

		// If search term is not set, list all the members
		if (!isset($this->params['url']['query'])) {
			return $this->redirect( array('controller' => 'members', 'action' => 'listMembers') );
		}

		$keyword = $this->params['url']['query'];

		$this->__paginateMemberList($this->Member->getMemberSummaryForSearchQuery(true, $keyword));
	}

/**
 * Perform all the actions needed to get a paginated member list with actions applied.
 *
 * @param array $queryResult The query to pass to paginate(), usually obtained from a Member::getMemberSummary**** method.
 */
	private function __paginateMemberList($queryResult) {
		$this->paginate = $queryResult;
		$memberList = $this->paginate('Member');
		$memberList = $this->Member->formatMemberInfoList($memberList, false);
		$memberList = $this->__addMemberActions($memberList);
		$this->set('memberList', $memberList);
	}

/**
 * Get a MailingList model.
 *
 * @return MailingListModel The MailingListModel.
 */
	public function getMailingList() {
		App::uses('MailingList', 'Model');
		return new MailingList();
	}

/**
 * Grab a users e-mail address and start the membership procedure.
 */
	public function register() {
		$this->MailingList = $this->getMailingList();
		// Need a list of mailing-lists that the user can opt-in to
		$mailingLists = $this->MailingList->listMailinglists(false);
		$this->set('mailingLists', $mailingLists);

		if ($this->request->is('post')) {
			$result = $this->Member->registerMember( $this->request->data );

			if ($result) {
				$status = $result['status'];

				if ($status != Status::PROSPECTIVE_MEMBER) {
					// User is already way past this membership stage, send them to the login page
					$this->Session->setFlash( 'User with that e-mail already exists.' );
					return $this->redirect( array('controller' => 'members', 'action' => 'login') );
				}

				$email = $result['email'];

				// E-mail the member admins for a created record
				if ($result['createdRecord'] === true) {
					$this->_sendEmail(
						$this->Member->getEmailsForMembersInGroup(Group::MEMBERSHIP_ADMIN),
						'New Prospective Member Notification',
						'notify_admins_member_added',
						array(
							'email' => $email,
						)
					);
				}

				$memberId = $result['memberId'];

				// But e-mail the member either-way
				$this->__sendProspectiveMemberEmail($memberId);

				$this->__setFlashFromMailingListInfo('Registration successful, please check your inbox.', Hash::get($result, 'mailingLists'));
				return $this->redirect(array( 'controller' => 'pages', 'action' => 'home'));
			} else {
				$this->Session->setFlash( 'Unable to register.' );
			}
		}
	}

/**
 * Set the session flash message based on the data returned from a function that updates the mailing list settings.
 *
 * @param string $initialMessage The message to show at the start of the flash, before any mailing list messages.
 * @param array $results The results from the function that updated the mailing list settings.
 */
	private function __setFlashFromMailingListInfo($initialMessage, $results) {
		$message = $initialMessage;
		if (is_array($results) && !empty($results)) {
			$message .= '\n';
			foreach ($results as $resultData) {
				$resultStr = '';

				if ($resultData['successful']) {
					$resultStr .= 'Successfully ';
				} else {
					$resultStr .= 'Unable to ';
				}

				if ($resultData['action'] == 'subscribe') {
					$resultStr .= 'subscribed to ';
				} else {
					$resultStr .= 'unsubscribed from ';
				}

				$resultStr .= $resultData['name'];

				$message .= $resultStr . '\n';
			}
		}

		$this->Session->setFlash( $message );
	}

/**
 * Allow a member to set-up their initial login details.
 *
 * @param int $id The id of the member whose details we want to set-up.
 */
	public function setupLogin($id = null) {
		if ($id == null) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}

		if ($this->request->is('post')) {
			try {
				if ($this->Member->setupLogin($id, $this->request->data)) {
					$this->Session->setFlash('Username and Password set, please login.');
					return $this->redirect(array( 'controller' => 'members', 'action' => 'login'));
				} else {
					$this->Session->setFlash('Unable to set username and password.');
				}
			} catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}

		// We seem to get redirected to this method after the login redirect that happens above.
		// So let's detect that case and send them where they should go:
		$loggedInMemberId = $this->Member->getIdForMember($this->Auth->user());
		if ($loggedInMemberId) {
			switch ($this->Member->getStatusForMember($loggedInMemberId)) {
				case Status::PRE_MEMBER_1:
					return $this->redirect(array('controller' => 'members', 'action' => 'setupDetails', $loggedInMemberId));
			}
		}
	}

/**
 * Allow a member who is logged in to set-up their contact details.
 *
 * @param int $id The id of the member whose contact details we want to set-up.
 */
	public function setupDetails($id = null) {
		// Can't do this if id isn't the same as that of the logged in user.
		if ($id == null || $id != $this->_getLoggedInMemberId()) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}

		if ($this->request->is('post')) {
			try {
				if ($this->Member->setupDetails($id, $this->request->data)) {
					$memberEmail = $this->Member->getEmailForMember($id);

					$this->Session->setFlash('Contact details saved.');

					$this->_sendEmail(
						$this->Member->getEmailsForMembersInGroup(Group::MEMBERSHIP_ADMIN),
						'New Member Contact Details',
						'notify_admins_check_contact_details',
						array(
							'email' => $memberEmail,
							'id' => $id,
						)
					);

					$this->_sendEmail(
						$memberEmail,
						'Contact Information Completed',
						'to_member_post_contact_update'
					);

					return $this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
				} else {
					$this->Session->setFlash('Unable to save contact details.');
				}
			} catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}
	}

/**
 * Reject the contact details a member has supplied, with a message to say why.
 *
 * @param int $id The id of the member whose contact details we're rejecting.
 */
	public function rejectDetails($id = null) {
		if ($id == null) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}

		$this->set('name', $this->Member->getUsernameForMember($id));

		if ($this->request->is('post')) {
			try {
				if ($this->Member->rejectDetails($id, $this->request->data, $this->_getLoggedInMemberId())) {
					$this->Session->setFlash('Member has been contacted.');

					$memberEmail = $this->Member->getEmailForMember($id);

					Controller::loadModel('MemberEmail');

					$this->_sendEmail(
						$memberEmail,
						'Issue With Contact Information',
						'to_member_contact_details_rejected',
						array(
							'reason' => $this->MemberEmail->getMessage( $this->request->data )
						)
					);

					return $this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
				} else {
					$this->Session->setFlash('Unable to set member status. Failed to reject details.');
				}
			} catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}
	}

/**
 * Accept the contact details a member has supplied.
 *
 * @param int $id The id of the member whose contact details we're accepting.
 */
	public function acceptDetails($id = null) {
		if ($id == null) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}

		$this->set('accounts', $this->Member->getReadableAccountList());
		$this->set('name', $this->Member->getUsernameForMember($id));

		if ($this->request->is('post') || $this->request->is('put')) {
			try {
				$memberDetails = $this->Member->acceptDetails($id, $this->request->data, $this->_getLoggedInMemberId());
				if ($memberDetails) {
					$this->Session->setFlash('Member details accepted.');

					$this->__sendSoDetailsToMember($id);

					$this->_sendEmail(
						$this->Member->getEmailsForMembersInGroup(Group::MEMBERSHIP_ADMIN),
						'Impending Payment',
						'notify_admins_payment_incoming',
						array(
							'memberId' => $id,
							'memberName' => sprintf('%s %s', $memberDetails['firstname'], $memberDetails['surname']),
							'memberEmail' => $memberDetails['email'],
							'memberPayRef' => $memberDetails['paymentRef'],
						)
					);

					return $this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
				} else {
					$this->Session->setFlash('Unable to update member details');
				}
			}
			catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}
	}

/**
 * Approve a membership and report back to the user.
 *
 * @param int $id The id of the member who we are approving.
 */
	public function approveMember($id = null) {
		try {
			if ($this->__approveMember($id)) {
				$this->Session->setFlash('Member has been approved.');
			} else {
				$this->Session->setFlash('Member details could not be updated.');
			}
		} catch (InvalidStatusException $e) {
			return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
		}

		return $this->redirect($this->referer());
	}

/**
 * Approve a membership.
 *
 * @param int $id The id of the member who we are approving.
 */
	private function __approveMember($id) {
		$adminId = $this->_getLoggedInMemberId();
		$memberDetails = $this->Member->approveMember($id, $adminId);
		if ($memberDetails) {
			$adminDetails = $this->Member->getMemberSummaryForMember($adminId);

			// Notify all the member admins
			$this->_sendEmail(
				$this->Member->getEmailsForMembersInGroup(Group::MEMBERSHIP_ADMIN),
				'Member Approved',
				'notify_admins_member_approved',
				array(
					'memberName' => sprintf('%s %s', $memberDetails['firstname'], $memberDetails['surname']),
					'memberEmail' => $memberDetails['email'],
					'memberId' => $id,
					'memberPin' => $memberDetails['pin'],
				)
			);

			// E-mail the member
			$this->_sendEmail(
				$memberDetails['email'],
				'Membership Complete',
				'to_member_access_details',
				array(
					'manLink' => Configure::read('hms_help_manual_url'),
					'outerDoorCode' => Configure::read('hms_access_street_door'),
					'innerDoorCode' => Configure::read('hms_access_inner_door'),
					'wifiSsid' => Configure::read('hms_access_wifi_ssid'),
					'wifiPass' => Configure::read('hms_access_wifi_password'),
				)
			);

			return true;
		} else {
			return false;
		}
	}

/**
 * Change a members password.
 *
 * @param int $id The id of the member whose password we are changing.
 */
	public function changePassword($id) {
		$memberInfo = $this->Member->getMemberSummaryForMember($id);
		if (!$memberInfo) {
			return $this->redirect($this->referer());
		}

		$adminId = $this->_getLoggedInMemberId();
		$this->set('id', $id);
		$this->set('name', $memberInfo['username']);
		$this->set('ownAccount', $adminId == $id);

		if ($this->request->is('post')) {
			try {
				if ($this->Member->changePassword($id, $adminId, $this->request->data)) {
					$this->Session->setFlash('Password updated.');
					return $this->redirect(array( 'controller' => 'members', 'action' => 'view', $id));
				} else {
					$this->Session->setFlash('Unable to update password.');
				}
			} catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			} catch (NotAuthorizedException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}
	}

/**
 * Generate or complete a forgot password request.
 *
 * @param string $guid The id of the request, may be null.
 */
	public function forgotPassword($guid = null) {
		if ($guid != null) {
			if (!ForgotPassword::isValidGuid($guid)) {
				$guid = null;
			}
		}

		$this->set('createRequest', $guid == null);

		if ($this->request->is('post')) {
			try {
				if ($guid == null) {
					$data = $this->Member->createForgotPassword($this->request->data);
					if ($data != false) {
						$this->_sendEmail(
							$data['email'],
							'Password Reset Request',
							'forgot_password',
							array(
								'id' => $data['id'],
							)
						);

						return $this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_sent'));
					} else {
						return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
					}
				} else {
					if ($this->Member->completeForgotPassword($guid, $this->request->data)) {
						$this->Session->setFlash('Password successfully set.');
						return $this->redirect(array('controller' => 'members', 'action' => 'login'));
					} else {
						$this->Session->setFlash('Unable to set password');
						return $this->redirect(array('controller' => 'pages', 'action' => 'forgot_password_error'));
					}
				}
			} catch (InvalidStatusException $e) {
				return $this->redirect(array('controller' => 'pages', 'action' => 'home'));
			}
		}
	}

/**
 * Send the 'prospective member' email to a member.
 *
 * @param int $id The id of the member to send the message to.
 */
	public function sendMembershipReminder($id = null) {
		if ($id != null) {
			if ($this->__sendProspectiveMemberEmail($id)) {
				$this->Session->setFlash('Member has been contacted');
			} else {
				$this->Session->setFlash('Unable to contact member');
			}
		}

		return $this->redirect($this->referer());
	}

/**
 * Send the 'prospective member' email to a member.
 *
 * @param int $memberId The id of the member to send the message to.
 * @return bool True if e-mail was sent.
 */
	private function __sendProspectiveMemberEmail($memberId) {
		$email = $this->Member->getEmailForMember($memberId);
		if ($email) {
			return $this->_sendEmail(
				$email,
				'Welcome to Nottingham Hackspace',
				'to_prospective_member',
				array(
					'memberId' => $memberId,
				)
			);
		}
		return false;
	}

/**
 * Send the 'contact details reminder' email to a member.
 *
 * @param int $id The id of the member to contact.
 */
	public function sendContactDetailsReminder($id = null) {
		$emailSent = false;

		$email = $this->Member->getEmailForMember($id);
		if ($email) {
			$emailSent = $this->_sendEmail(
				$email,
				'Membership Info',
				'to_member_contact_details_reminder',
				array(
					'memberId' => $id,
				)
			);
		}

		if ($emailSent) {
			$this->Session->setFlash('Member has been contacted');
		} else {
			$this->Session->setFlash('Unable to contact member');
		}

		return $this->redirect($this->referer());
	}

/**
 * Send the 'so details reminder' email to a member.
 *
 * @param int $id The id of the member to contact.
 */
	public function sendSoDetailsReminder($id = null) {
		if ($this->__sendSoDetailsToMember($id)) {
			$this->Session->setFlash('Member has been contacted');
		} else {
			$this->Session->setFlash('Unable to contact member');
		}
		return $this->redirect($this->referer());
	}

/**
 * Send the e-mail containing standing order info to a member.
 *
 * @param int $memberId The id of the member to send the reminder to.
 * @return bool True if mail was sent, false otherwise.
 */
	private function __sendSoDetailsToMember($memberId) {
		$memberSoDetails = $this->Member->getSoDetails($memberId);
		if ($memberSoDetails != null) {
			return $this->_sendEmail(
				$memberSoDetails['email'],
				'Bank Details',
				'to_member_so_details',
				array(
					'name' => sprintf('%s %s', $memberSoDetails['firstname'], $memberSoDetails['surname']),
					'paymentRef' => $memberSoDetails['paymentRef'],
					'accountNum' => Configure::read('hms_so_accountNumber'),
					'sortCode' => Configure::read('hms_so_sortCode'),
					'accountName' => Configure::read('hms_so_accountName'),
				)
			);
		}

		return false;
	}

/**
 * View the full member profile.
 *
 * @param int $id The id of the member to view.
 */
	public function view($id) {
		$showAdminFeatures = false;
		$showFinances = false;
		$hasJoined = false;
		$showPersonalDetails = false;
		$canView = $this->__getViewPermissions($id, $showAdminFeatures, $showFinances, $hasJoined, $showPersonalDetails);

		if ($canView) {
			$rawMemberInfo = $this->Member->getMemberSummaryForMember($id, false);

			if ($rawMemberInfo) {
				$memberEmail = $this->Member->getEmailForMember($rawMemberInfo);
				$this->MailingList = $this->getMailingList();
				// Need a list of mailing-lists that the user can opt-in to
				$mailingLists = $this->MailingList->getListsAndSubscribeStatus($memberEmail);
				$this->set('mailingLists', $mailingLists);

				$sanitisedMemberInfo = $this->Member->sanitiseMemberInfo($rawMemberInfo, $showAdminFeatures, $showFinances, $hasJoined, true, true, $showPersonalDetails);
				if ($sanitisedMemberInfo) {
					$formattedInfo = $this->Member->formatMemberInfo($sanitisedMemberInfo, true);
					if ($formattedInfo) {
						if ($showAdminFeatures) {
							// Grab the data for the last e-mail
							Controller::loadModel('EmailRecord');
							$lastEmailRecord = $this->EmailRecord->getMostRecentEmailForMember($id);
							if ($lastEmailRecord != null) {
								$formattedInfo['lastEmail'] = $lastEmailRecord;

								$this->Nav->add('View Email History', 'emailRecords', 'view', array($id));
							}
						}

						$this->set('member', $formattedInfo);

						$this->Nav->add('Edit', 'members', 'edit', array( $id ) );
						$this->Nav->add('Change Password', 'members', 'changePassword', array( $id ) );

						foreach ($this->__getActionsForMember($id) as $action) {
							$class = '';
							if (isset($action['class'])) {
								$class = $action['class'];
							}
							$this->Nav->add($action['title'], $action['controller'], $action['action'], $action['params'], $class);
						}

						return; // Don't hit that redirect
					}
				}
			}
		}

		// Nope, not allowed to view that
		return $this->redirect($this->referer());
	}

/**
 * Allow editing of a member profile.
 *
 * @param int $id The id of the member to view.
 */
	public function edit($id) {
		$showAdminFeatures = false;
		$showFinances = false;
		$hasJoined = false;
		$showPersonalDetails = false;
		$canEdit = $this->__getViewPermissions($id, $showAdminFeatures, $showFinances, $hasJoined, $showPersonalDetails);
		if ($canEdit) {
			$rawMemberInfo = $this->Member->getMemberSummaryForMember($id, false);
			if ($rawMemberInfo) {
				$memberEmail = $this->Member->getEmailForMember($rawMemberInfo);
				$this->MailingList = $this->getMailingList();
				// Need a list of mailing-lists that the user can opt-in to
				$mailingLists = $this->MailingList->getListsAndSubscribeStatus($memberEmail);
				$this->set('mailingLists', $mailingLists);

				$sanitisedMemberInfo = $this->Member->sanitiseMemberInfo($rawMemberInfo, $showAdminFeatures, $showFinances, $hasJoined, $showAdminFeatures, true, $showPersonalDetails);
				$formattedMemberInfo = $this->Member->formatMemberInfo($sanitisedMemberInfo, true);
				if ($formattedMemberInfo) {
					$this->set('member', $formattedMemberInfo);
					$this->set('accounts', $this->Member->getReadableAccountList());
					$this->set('groups', $this->Member->Group->getGroupList());

					if ( $this->request->is('post') || $this->request->is('put')) {
						$sanitisedData = $this->Member->sanitiseMemberInfo($this->request->data, $showAdminFeatures, $showFinances, $hasJoined, $showAdminFeatures, false, $showPersonalDetails);
						if ($sanitisedData) {
							$updateResult = $this->Member->updateDetails($id, $sanitisedData, $this->_getLoggedInMemberId());
							if (is_array($updateResult)) {
								$this->__setFlashFromMailingListInfo('Details updated.', $updateResult['mailingLists']);
								return $this->redirect(array('action' => 'view', $id));
							}
						}
						$this->Session->setFlash('Unable to update details.');
					}

					if (!$this->request->data) {
						$this->request->data = $rawMemberInfo;
					}
				}
			}
		} else {
			// Couldn't find a record with that id...
			return $this->redirect($this->referer());
		}
	}

/**
 * Revoke a members membership.
 *
 * @param int $id The id of the member to revoke.
 */
	public function revokeMembership($id = null) {
		try {
			if ($this->Member->revokeMembership($id, $this->_getLoggedInMemberId())) {
				$this->Session->setFlash('Membership revoked.');
			} else {
				$this->Session->setFlash('Unable to revoke membership.');
			}
		} catch(InvalidStatusException $ex) {
			$this->Session->setFlash('Only current members can have their membership revoked.');
		} catch(NotAuthorizedException $ex) {
			$this->Session->setFlash('You are not authorized to do that.');
		}

		return $this->redirect($this->referer());
	}

/**
 * Reinstate an ex-members membership.
 *
 * @param int $id The id of the member to reinstate.
 */
	public function reinstateMembership($id = null) {
		try {
			if ($this->Member->reinstateMembership($id, $this->_getLoggedInMemberId())) {
				$this->Session->setFlash('Membership reinstated.');
			} else {
				$this->Session->setFlash('Unable to reinstate membership.');
			}
		} catch(InvalidStatusException $ex) {
			$this->Session->setFlash('Only ex members can have their membership reinstated.');
		} catch(NotAuthorizedException $ex) {
			$this->Session->setFlash('You are not authorized to do that.');
		}

		return $this->redirect($this->referer());
	}

/** 
 * Check to see if certain view/edit params should be shown to the logged in member.
 * 
 * @param int $memberId The id of the member being viewed.
 * @param bool $showAdminFeatures (out) If this is set to true then admin features should be shown.
 * @param bool $showFinances (out) If this is set to true then financial information should be shown.
 * @param bool $hasJoined (out) If this is set to true then the member has joined.
 * @param bool $showPersonalDetails (out) If this is set to true then show personal information about the member.
 */
	private function __getViewPermissions($memberId, &$showAdminFeatures, &$showFinances, &$hasJoined, &$showPersonalDetails) {
		if (is_numeric($memberId)) {
			$memberStatus = $this->Member->getStatusForMember($memberId);
			if ($memberStatus) {
				$viewerId = $this->_getLoggedInMemberId();

				$memberHasFullAccess = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::FULL_ACCESS);
				$memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_ADMIN);
				$memberIsOnMembershipTeam = $this->Member->GroupsMember->isMemberInGroup($viewerId, Group::MEMBERSHIP_TEAM);

				$showAdminFeatures = ($memberHasFullAccess || $memberIsMembershipAdmin || $memberIsOnMembershipTeam);

				// Only show the finance stuff to admins, current or ex members
				$hasJoined = in_array($memberStatus, array(Status::CURRENT_MEMBER, Status::EX_MEMBER));

				$viewingOwnProfile = $viewerId == $memberId;

				$showPersonalDetails = ($memberHasFullAccess || $memberIsMembershipAdmin || $viewingOwnProfile);

				$showFinances = ($showAdminFeatures || $viewingOwnProfile) && $hasJoined;

				return ($viewingOwnProfile || $showAdminFeatures);
			}
		}
		return false;
	}

/**
 * Send an e-mail to every member with a certain status.
 *
 * @param int $statusId Attempt to e-mail members with this status.
 */
	public function emailMembersWithStatus($statusId) {
		$memberList = $this->Member->getMemberSummaryForStatus(false, $statusId);
		$statusInfo = $this->Member->Status->getStatusSummaryForId($statusId);

		$this->set('members', $memberList);
		$this->set('status', $statusInfo);

		if ($memberList && $statusInfo) {
			if ($this->request->is('post')) {
				$messageDetails = $this->Member->validateEmail($this->request->data);

				if ($messageDetails) {
					$failedMembers = array();
					foreach ($memberList as $member) {
						$emailOk = $this->_sendEmail(
							$member['email'],
							$messageDetails['subject'],
							'default',
							array(
								'content' => $messageDetails['message']
							)
						);
						if (!$emailOk) {
							array_push($failedMembers, $member);
						}
					}

					$numFailed = count($failedMembers);
					if ($numFailed > 0) {
						if ($numFailed == count($memberList)) {
							$this->Session->setFlash('Failed to send e-mail to any member.');
						} else {
							$flashMessage = 'E-mail sent to all but the following members:';
							foreach ($failedMembers as $failedMember) {
								$flashMessage .= '\n' . $failedMember['name'];
							}
							$this->Session->setFlash($flashMessage);
						}
					} else {
						$this->Session->setFlash('E-mail sent to all listed members.');
					}
					return $this->redirect(array('controller' => 'members', 'action' => 'index'));
				}
			}
		} else {
			return $this->redirect($this->referer());
		}
	}

/**
 * Attempt to login as a member.
 */
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				return $this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'));
			}
		}
	}

/**
 * Logout.
 */
	public function logout() {
		return $this->redirect($this->Auth->logout());
	}

/**
 * Adds the appropriate actions to each member in the member list.
 * 
 * @param array $memberList A list of member summaries to add the actions to.
 * @return array The original memberList, with the actions added for each member.
 */
	private function __addMemberActions($memberList) {
		// Have to add the actions ourselves
		$numMembers = count($memberList);
		for ($i = 0; $i < $numMembers; $i++) {
			$id = $memberList[$i]['id'];
			$memberList[$i]['actions'] = $this->__getActionsForMember($id);
		}

		return $memberList;
	}

/**
 * Get an array of possible actions for a member
 * 
 * @param int $memberId The id of the member to work with.
 * @return array An array of actions.
 */
	private function __getActionsForMember($memberId) {
		$statusId = $this->Member->getStatusForMember($memberId);

		$actions = array();
		switch($statusId) {
			case Status::PROSPECTIVE_MEMBER:
				array_push($actions,
					array(
						'title' => 'Send Membership Reminder',
						'controller' => 'members',
						'action' => 'sendMembershipReminder',
						'params' => array(
							$memberId,
						),
					)
				);
			break;

			case Status::PRE_MEMBER_1:
				array_push($actions,
					array(
						'title' => 'Send Contact Details Reminder',
						'controller' => 'members',
						'action' => 'sendContactDetailsReminder',
						'params' => array(
							$memberId,
						),
					)
				);
			break;

			case Status::PRE_MEMBER_2:
				array_push($actions,
					array(
						'title' => 'Accept Details',
						'controller' => 'members',
						'action' => 'acceptDetails',
						'params' => array(
							$memberId,
						),
						'class' => 'positive',
					),

					array(
						'title' => 'Reject Details',
						'controller' => 'members',
						'action' => 'rejectDetails',
						'params' => array(
							$memberId,
						),
						'class' => 'negative',
					)
				);
			break;

			case Status::PRE_MEMBER_3:

				array_push($actions,
					array(
						'title' => 'Send SO Details Reminder',
						'controller' => 'members',
						'action' => 'sendSoDetailsReminder',
						'params' => array(
							$memberId,
						),
					)
				);

				array_push($actions,
					array(
						'title' => 'Approve Member',
						'controller' => 'members',
						'action' => 'approveMember',
						'params' => array(
							$memberId,
						),
						'class' => 'positive attention',
					)
				);

			break;

			case Status::CURRENT_MEMBER:
				array_push($actions,
					array(
						'title' => 'Revoke Membership',
						'controller' => 'members',
						'action' => 'revokeMembership',
						'params' => array(
							$memberId,
						),
						'class' => 'negative',
					)
				);
			break;

			case Status::EX_MEMBER:
				array_push($actions,
					array(
						'title' => 'Reinstate Membership',
						'controller' => 'members',
						'action' => 'reinstateMembership',
						'params' => array(
							$memberId,
						),
						'class' => 'positive',
					)
				);
			break;
		}

		return $actions;
	}

/**
 * Test to see if a request is coming from within the hackspace.
 *
 * @return bool True if the request is coming from with in the hackspace, false otherwise.
 */
	public function isRequestLocal() {
		return preg_match('/10\.0\.0\.\d+/', $this->getRequestIpAddress());
	}

/**
 * Get the ip address of the request.
 *
 * @return string The IP address of the request.
 */
	public function getRequestIpAddress() {
		// We might have a debug config here to force requests to be local
		App::uses('PhpReader', 'Configure');
		Configure::config('default', new PhpReader());

		try {
			Configure::load('debug', 'default');
			$configIp = Configure::read('forceRequestIp');
			if (isset($configIp)) {
				return $configIp;
			}
		} catch(ConfigureException $ex) {
			// We don't care.
		}

		return $_SERVER["SERVER_ADDR"];
	}

/**
 * Upload a .csv file of bank transactions and look for members to approve.
 *
 * @param string $guid If set then look here in the session for a list of account id's to approve.
 */
	public function uploadCsv($guid = null) {
		$validMemberIds = array();
		$preview = true;

		// If the guid is not set then we should show the upload form
		if ($guid == null) {
			// Has a file been uploaded?
			if ($this->request->is('post')) {
				// Ok, read the file
				Controller::loadModel('FileUpload');

				$filename = $this->FileUpload->getTmpName($this->request->data);

				if ($this->BankStatement->readfile($filename)) {
					// It seems ot be a valid .csv, grab all the payment references
					$payRefs = array();
					$this->BankStatement->iterate(function ($transaction) use(&$payRefs) {
						$ref = $transaction['ref'];

						if (is_string($ref) && strlen($ref) > 0) {
							array_push($payRefs, $ref);
						}
					});

					// Ok so we have a list of payment refs, get the account id's from them
					$accountIds = $this->Member->Account->getAccountIdsForRefs($payRefs);
					if ( is_array($accountIds) && count($accountIds) > 0) {
						// Ok now we need the rest of the member info
						$members = $this->Member->getMemberSummaryForAccountIds(false, $accountIds);

						// We only want members who we're waiting for payments from (Pre-Member 3)
						foreach ($members as $member) {
							if (Hash::get($member, 'status.id') == Status::PRE_MEMBER_3) {
								array_push(
									$validMemberIds,
									$member['id']
								);
							}
						}
					}

					// Did we find any? If so write them to the session with a guid.
					// This is 100% not dodgy... Honest.
					if (count($validMemberIds) > 0) {
						$guid = $this->getMemberIdSessionKey();
						$this->Session->write($guid, $validMemberIds);

						$this->Nav->add('Approve All', 'members', 'uploadCsv', array($guid), 'positive');
					} else {
						$this->Session->setFlash('No new member payments in .csv.');
						return $this->redirect(array('controller' => 'members', 'action' => 'index'));
					}
				} else {
					// Invalid file
					$this->Session->setFlash('That did not seem to be a valid bank .csv file');
					return $this->redirect(array('controller' => 'members', 'action' => 'uploadCsv'));
				}
			}
		} else {
			// Guid exists, is it valid?
			if ($this->Session->check($guid)) {
				// Yup then grab the member ids
				$validMemberIds = $this->Session->read($guid);
				$preview = false;
			} else {
				// Invalid guid, redirect to upload
				return $this->redirect(array('controller' => 'members', 'action' => 'uploadCsv'));
			}
		}

		// If there are no valid members then just show the upload form
		if (count($validMemberIds) <= 0) {
			$this->set('memberList', null);
		} else {
			// Grab the member info, this might actually be the second time we're doing this if we've just uploaded.
			$members = $this->Member->getMemberSummaryForMembers($validMemberIds, true);
			$this->set('memberList', $members);

			// If we're not previewing, they do the actual approval
			if (!$preview) {
				$flash = '';
				// Actually approve the members
				foreach ($members as $member) {
					if ($this->__approveMember($member['id'])) {
						$flash .= 'Successfully approved';
					} else {
						$flash .= 'Unable to approve';
					}

					$flash .= sprintf(' member %s %s\n', $member['firstname'], $member['surname']);
				}

				$this->Session->delete($guid);

				$this->Session->setFlash($flash);
				return $this->redirect(array('controller' => 'members', 'action' => 'index'));
			}
		}
	}

/**
 * Get the key used to store members ids in the session after uploading a csv.
 * 
 * @return string The key to be used in the session.
 * @link MemberController::uploadCsv
 */
	public function getMemberIdSessionKey() {
		return String::uuid();
	}
}