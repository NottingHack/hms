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
 * Controller for the Bank Transactions, handles upload of CSV's and 
 * showing an Accounts Transaction history
 */
class BankTransactionsController extends AppController {

/**
 * List of models this controller uses.
 * @var array
 */
    public $uses = array('BankTransaction');
    
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
        
		$authGranted = false;

		// Only history page implemented so far
		if ($request->params['action'] == 'history') {
            // Get the member_id details have been requested for & the logged in users member_id
            if (isset($request->params['pass'][0])) {
                $reqMemberId = $request->params['pass'][0];
            } else {
                $reqMemberId = $memberId;
            }

            // Allow everyone to view their own transaction history
            if ($reqMemberId == $memberId) {
                $authGranted = true;
            } elseif ($this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_ADMIN )) {
                // Only allow 'Full Access' (via parent::isAuthorized) and 'Membership Admins' to view the transaction history of others
                $authGranted = true;
            }
        }
        
        $memberIsMembershipAdmin = $this->Member->GroupsMember->isMemberInGroup( $memberId, Group::MEMBERSHIP_ADMIN );
        
        switch ($request->action) {
            case 'uploadCsv':
                return $memberIsMembershipAdmin;
        
        }
		return $authGranted;
	}
    
/**
 *
    public function index() {
        // notthing to see here
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

						$this->Nav->add('Approve All', 'banktransactions', 'uploadCsv', array($guid), 'positive');
					} else {
						$this->Session->setFlash('No new member payments in .csv.');
						return $this->redirect(array('controller' => 'members', 'action' => 'index'));
					}
				} else {
					// Invalid file
					$this->Session->setFlash('That did not seem to be a valid bank .csv file');
					return $this->redirect(array('controller' => 'banktransactions', 'action' => 'uploadCsv'));
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
				return $this->redirect(array('controller' => 'banktransactions', 'action' => 'uploadCsv'));
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
    
/**
 * Show a list of all transactions for $memberId, or for the logged in member if $memberId isn't set.
 * @param int|null $memberId The members id to list all transactions for
 */
	public function history($memberId = null) {
		$this->loadModel('BankTransaction');

		if ($memberId == null) {
			$memberId = $this->_getLoggedInMemberId();
		}
        // grab account_id from member_id
        $this->Member->id = $memberId;
        $accountId = $this->Member->field('account_id');
        
        $this->__transactionList($accountId);

	}

/**
 * List all transactions for a given member
 *
 * @param int $memberId The members id to list all transactions for
 */
	private function __transactionList($accountId) {
		$this->paginate = $this->BankTransaction->getBankTransactionList(true, array('AccountBT.account_id' => $accountId));
		$bankTransactionsList = $this->paginate('BankTransaction');
		$this->set('bankTransactionsList', $bankTransactionsList);
	}
    
}