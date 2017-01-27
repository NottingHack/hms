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

/**
 * Controller to handle Member rfid cards
 */
class RfidTagsController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The list of models this Controller relies on.
 * @var array
 */
	public $uses = array('RfidTag', 'Member');

/**
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		// allows full access to see everything
		if (parent::isAuthorized($user, $request)) {
			return true;
		}

		// Get the member_id details have been requested for & the logged in users member_id
		$logMemberId = $this->_getLoggedInMemberId();
		if (isset($request->params['pass'][0])) {
			$reqMemberId = $request->params['pass'][0];
		} else {
			$reqMemberId = $logMemberId;
		}

		$memberAdmin = $this->Member->GroupsMember->isMemberInGroup( $logMemberId, Group::MEMBERSHIP_ADMIN);

		switch ($request->action) {
			case 'view':
				// Allow everyone to view their own rfid cards
				if ($reqMemberId == $logMemberId or $memberAdmin) {
					return true;
				}
				return false;
			case 'edit':
				// we'll sort this out later
				return true;
		}
        
	}

/**
 * Show a list of all registered RFID tags for the supplied user
 *
 * @param int|null $memberId The id fo the member to view; null grabs the currently logged-in user id instead
 */
	public function view($memberId = null) {

		if ($memberId == null) {
			$memberId = $this->_getLoggedInMemberId();
		}

		$this->__tagsList($memberId);

		$member = $this->Member->getMemberSummaryForMember($memberId);
		$this->set('member', $member);

	}

/**
 * Edits a single, registered RFID tag
 *
 * @param int|null $rfidSerial The serial number of the card to edit
 */
	public function edit($rfidId = null) {

		$this->set('states', $this->RfidTag->statusStrings);

		// if there wasn't a serial passed in, just punt the user back to their list of registered cards
		if ($rfidId == null) {
			return $this->redirect(array('controller' => 'rfidTags', 'action' => 'view'));
		}
		$id = $this->RfidTag->getMemberIdForTag($rfidId);

		$member = $this->Member->getMemberSummaryForMember($id);
		$this->set('member', $member);
		// if this is our member id, or the current member is an admin
		$canView = $this->__getViewPermissions($id);
	
		if ($canView == true) {
			$rawTagDetails = $this->RfidTag->getDetailsForTag($rfidId, false);
			$formattedTagDetails = $this->RfidTag->formatDetails($rawTagDetails);

			if ($formattedTagDetails)
			{
				$this->set('rfidTagDetails', $formattedTagDetails);

				// if this is a POST/PUT:
				if ($this->request->is('post') || $this->request->is('put')) {
					// sanitise!
					$sanitisedData = $this->request->data;
					$sanitisedData['RfidTag']['friendly_name'] = 
						(strlen($sanitisedData['RfidTag']['friendly_name']) > 0 ? $sanitisedData['RfidTag']['friendly_name'] : null);

					//var_dump($sanitisedData);

					if ($sanitisedData) {
						// persist data to the table
						$updateResult = $this->RfidTag->save($sanitisedData);
						if ($updateResult) {
							// set flash
							$this->Session->setFlash('Card updated successfully.');
							// goto list view
							return $this->redirect(array('controller' => 'rfidTags', 'action' => 'view', $member['id']));
						}
					}

					$this->Session->setFlash('Unable to update card.');
				}

				// if this is a GET:
				if (!$this->request->data) {
					// load card details for editing
					$this->request->data = $rawTagDetails;
				}
			}
		}
		else {	// NOT owner or admin:
			$this->Session->setFlash('Not owner or admin');

			// redirect to users' own card list
			return $this->redirect(array('controller' => 'rfidTags', 'action' => 'view'));
		}
	}

/**
 * List all tags for a given member
 *
 * @param int $memberId The members id to list all tags for
 */
	private function __tagsList($memberId) {
		$this->paginate = $this->RfidTag->getRfidTagList(true, array('Member.member_id' => $memberId));
		$tagsList = $this->paginate('RfidTag');
		$tagsList = $this->RfidTag->formatRfidTagList($tagsList, false);
		$this->set('tagsList', $tagsList);
	}

/**
 * Check to see if certain view/edit params should be shown to the logged in member.
 *
 * @param int $memberId The id of the member being viewed.
 */
	private function __getViewPermissions($memberId) {
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

				return ($showAdminFeatures || ($viewingOwnProfile && $hasJoined));
			}
		}
		return false;
	}

}
