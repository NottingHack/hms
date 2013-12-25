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
 * Controller to handle Snackspace functionality, allows a member to see their transaction history.
 */
class SnackspaceController extends AppController {

/**
 * List of models this controller uses.
 * @var array
 */
	public $uses = array('Transactions');

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

		$authGranted = false;

		// Only history page implemented so far
		if ($request->params['action'] != 'history') {
			return false;
		}

		// Get the member_id details have been requested for & the logged in users member_id
		$logMemberId = $this->_getLoggedInMemberId();
		if (isset($request->params['pass'][0])) {
			$reqMemberId = $request->params['pass'][0];
		} else {
			$reqMemberId = $logMemberId;
		}

		// Allow everyone to view their own transaction history
		if ($reqMemberId == $logMemberId) {
			$authGranted = true;
		} elseif ($this->Member->GroupsMember->isMemberInGroup( $logMemberId, Group::SNACKSPACE_ADMIN )) {
			// Only allow 'Full Access' (via parent::isAuthorized) and 'Snackspace Admins' to view the transaction history of others
			$authGranted = true;
		}

		return $authGranted;
	}

/**
 * Show a list of all transactions for $memberId, or for the logged in member if $memberId isn't set.
 * @param int|null $memberId The members id to list all transactions for
 */
	public function history($memberId = null) {
		$this->loadModel('Transactions');

		if ($memberId == null) {
			$memberId = $this->_getLoggedInMemberId();
		}

		$this->__transactionList($memberId);

		$this->loadModel('Member');
		$balance = $this->Member->getBalanceForMember($memberId);
		$this->set('balance', $balance);
	}

/**
 * List all transactions for a given member
 *
 * @param int $memberId The members id to list all transactions for
 */
	private function __transactionList($memberId) {
		$this->paginate = $this->Transactions->getTransactionList(true, array('Member.member_id' => $memberId));
		$transactionsList = $this->paginate('Transactions');
		$this->set('transactionsList', $transactionsList);
	}

}
