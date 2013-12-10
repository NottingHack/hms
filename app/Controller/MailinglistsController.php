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
 * Controller to handle MailingList functionality, allows a user
 * to see a list of mailing lists, and subscribe/un-subscribe e-mail addresses.
 */
class MailingListsController extends AppController {

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
		return true;
	}

/**
 * Show a list of all mailing list and some basic stats for each of them.
 */
	public function index() {
		$result = $this->MailingList->listMailinglists();
		$mailingLists = $result['data'];

		$memberEmails = $this->__getMemberEmails();

		$numMailingLists = count($mailingLists);
		for ($i = 0; $i < $numMailingLists; $i++) {
			$mailingLists[$i] = $this->__getListStats($mailingLists[$i], $memberEmails);
		}

		$this->set('mailingLists', $mailingLists);
		$this->Nav->addExternal('Edit Lists', 'https://admin.mailchimp.com/lists/' );
	}

/**
 * View details about a mailing list
 * @param  int|null $id The id of the mailing list to look at.
 */
	public function view($id = null) {
		$memberEmails = $this->__getMemberEmails();
		$result = $this->MailingList->listMailinglists($id, false);
		$mailingList = $this->__getListStats($result['data'][0], $memberEmails);

		$membersSubscribed = array();
		$membersNotSubscribed = array();

		$this->loadModel('Member');
		$allMembers = $this->Member->getMemberSummaryAll(false);

		foreach ($allMembers as $memberInfo) {
			if (in_array($memberInfo['email'], $mailingList['stats']['hms_members'])) {
				array_push($membersSubscribed, $memberInfo);
			} else {
				array_push($membersNotSubscribed, $memberInfo);
			}
		}

		$this->set('mailingList', $mailingList);
		$this->set('membersSubscribed', $membersSubscribed);
		$this->set('membersNotSubscribed', $membersNotSubscribed);

		$this->Nav->addExternal('Edit List', 'https://admin.mailchimp.com/lists/' );
	}

/**
 * Given information about a mailing list and a list of member e-mail, return
 * the array of mailing list data with information about the number of subscribed
 * members etc.
 * @param  array $list The array of mailing list data.
 * @param  array $memberEmails The list of member e-mail addresses.
 * @return array The array of mailing list data, with extra infomration added.
 */
	private function __getListStats($list, $memberEmails) {
		$subscribersResult = $this->MailingList->listSubscribers($list['id'], false);
		$subscriberEmails = Hash::extract($subscribersResult['data'], '{n}.email');
		$memberSubscriberEmails = array_intersect($memberEmails, $subscriberEmails);

		$list['stats']['hms_member_count'] = count($memberSubscriberEmails);
		$list['stats']['hms_members'] = $memberSubscriberEmails;

		return $list;
	}

/**
 * Get a list of all member e-mail addresses.
 * @return string[] A list of all member e-mail addresses.
 */
	private function __getMemberEmails() {
		$this->loadModel('Member');
		$memberEmails = $this->Member->getEmailsForAllMembers();
		return $memberEmails;
	}
}