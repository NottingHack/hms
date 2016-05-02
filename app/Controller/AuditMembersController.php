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
App::uses('CakeEmail', 'Network/Email');
    
/**
 * Controller to handle Member audit functionality.
 *
 */
class AuditMembersController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The list of models this Controller relies on.
 * @var array
 */
	public $uses = array('Member', 'BankTransaction', 'Meta');

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
        
        // only full access can can go near this for now
        
        return false;
		
	}
    
/**
 *
 */
    public function audit() {
        // TODO: this is going to need some good auto gen data in dev and a boat load of CSV loading in live before ever getting executed
        
        // get the latest transaction date for all acounts, store in $latestTransactionDateForAccounts
        $btOptions = array(
           'fields' => array(
                             'MAX(BankTransaction.transaction_date) AS transaction_date',
                             'BankTransaction.account_id',
                             ),

            'conditions' => array(
                                  'BankTransaction.account_id NOT' => 'NULL',
                                  ),
            'group' => array(
                             'BankTransaction.account_id'
                             ),
            'recursive' => -1,
        );
        
        $bt = $this->BankTransaction->find('all', $btOptions);
        
        /*
            Results data format
            [account_id] => transaction_date
         */
        $latestTransactionDateForAccounts = Hash::combine($bt, '{n}.BankTransaction.account_id', '{n}.{n}.transaction_date');
        
        // get the list of memberId and there satus for accounts, store in $memberIdsAndStatusForAccounts
        $mOptions = array(
            'fields' => array(
                              'Member.member_id',
                              'Member.member_status',
                              'Member.account_id',
                              ),
            'conditions' => array(
                                  'Member.member_status' => array(
                                                                  Status::PRE_MEMBER_3,
                                                                  Status::CURRENT_MEMBER,
                                                                  Status::EX_MEMBER
                                                                  ),
                         
                         ),
            'recursive' => -1
        );
        
        /*
            Results data format
            [account_id] => 
                array(
                    [member_id] => member_status
                )
         */
        $memberIdsAndStatusForAccounts = $this->Member->find('list', $mOptions);
        
        // now we have the data we need from the DB
        
        $approveIds = array();
        $warnIds = array();
        $revokeIds = array();
        $reinstateIds = array();
        $ohCrapIds = array();

        $dateNow = new DateTime(); // this will be the server time the we run, might need to shift time portion to end of the day 23:59
        $dateNow->setTime(0,0,0);
        $warnDate = clone $dateNow;
        $warnDate->sub(new DateInterval($this->Meta->getValueFor('audit_warn_interval')));
        $revokeDate = clone $dateNow;
        $revokeDate->sub(new DateInterval($this->Meta->getValueFor('audit_revoke_interval')));
        
        // now we can work through the data and apply audit logic
        foreach ($memberIdsAndStatusForAccounts as $accountId => $membersForAccount) {
            if (isset($latestTransactionDateForAccounts[$accountId])) {
                $transactionDate = new DateTime($latestTransactionDateForAccounts[$accountId]);
            } else {
                $transactionDate = null;
            }
            
            
            foreach ($membersForAccount as $memberId => $memberStatus) {
                // either switch on transaction age or on status???
                switch ($memberStatus) {
                    case Status::PRE_MEMBER_3:
                        if ($transactionDate === null) {
                            break; // not paid us yet notting to do here
                        } elseif ($transactionDate > $revokeDate) { // transaction date is newer than revoke date
                            // approve member
                            array_push($approveIds, $memberId);
                        } else { // transaction date is older than revoke date
                            // why have they not yet been approved yet tell the admins
                            array_push($ohCrapIds, $memberId);
                        }
                        break;

                    case Status::CURRENT_MEMBER:
                        if ($transactionDate === null) {
                            // current member that has never paid us?
                            // tell the admins
                            array_push($ohCrapIds, $memberId);
                        } elseif ($transactionDate < $revokeDate) { // transaction date is older than revoke date
                            // make ex member
                            array_push($revokeIds, $memberId);
                        } elseif ($transactionDate < $warnDate) { // transaction date is older than warning date
                            // if not all ready warned
                                // warn membership may be terminated if we dont see one soon
                            array_push($warnIds, $memberId);
                        }
                        // date diff should be less than 1.5 months
                        break;

                    case Status::EX_MEMBER:
                        if ($transactionDate > $revokeDate){ // transaction date is newer than revoke date
                            // reinstate member
                            array_push($reinstateIds, $memberId);
                        }
                        break;
                    default:
                        // should never get here
                        break;
                }
            }
        }
        
        // right should now have 5 arrays of Id's to go and process
        // by batching the id's we can send just one email to membership team with tables of members
        // showing diffrent bits of info for diffrent states
        // approve, name, emial, pin, joint?
        // warn, name, email, last payment date, ref, last visit date, joint?
        // revoke, name, email, last payment date, ref, last visit date, joint?
        // reactivte, name, emial, date they were made ex, last visit date, joint?
        // ohcrap list to software@, member_id
        
//        debug("Now: " . $dateNow->format('Y-m-d'));
//        debug("Warn: " . $warnDate->format('Y-m-d'));
//        debug("Revoke: " . $revokeDate->format('Y-m-d'));
        
        $adminId = $this->_getLoggedInMemberId();
        
//        debug("Approve");
//        debug($approveIds);
        foreach ($approveIds as $memberId) {
            $this->__aproveMember($memberId, $adminId);
        }
//        debug("Warn");
//        debug($warnIds);
        foreach ($warnIds as $memberId) {
            // $this->__warnMember($memberId);
        }
//        debug("Revoke");
//        debug($revokeIds);
        foreach ($revokeIds as $memberId) {
            $this->__revokeMember($memberId, $adminId);
        }
//        debug("Reinstate");
//        debug($reinstateIds);
        foreach ($reinstateIds as $memberId) {
            $this->__reinstateMember($memberId, $adminId);
        }

//        debug($ohCrapIds);
        if (count($ohCrapIds) != 0) {
            $this->__sendSoftwareEmail($ohCrapIds);
        }
    
        $membershipTeamEmail = $this->Meta->getValueFor('membership_email');
        $subject = 'HMS Audit results';
        $template = 'notify_admins_audit';
        
        $this->_sendEmail(
                          $membershipTeamEmail,
                          $subject,
                          $template,
                          array(
                                'approveMembers' => $this->Member->getMemberSummaryForMembers($approveIds),
                                'warnedMembers' => $this->Member->getMemberSummaryForMembers($warnIds),
                                'revokedMembers' => $this->Member->getMemberSummaryForMembers($revokeIds),
                                'reinstatedMembers' => $this->Member->getMemberSummaryForMembers($reinstateIds),
                                )
                          );
        
        
        $this->Session->setFlash('Audit complete');
        
        return; //$this->redirect(array( 'controller' => 'members'));
        
    }
    
/**
 * Approve a membership.
 *
 * @param int $memberId The id of the member who we are approving.
 */
    private function __aproveMember($memberId, $adminId) {
		$memberDetails = $this->Member->approveMember($memberId, $adminId);
		if ($memberDetails) {
            return $this->__sendMembershipCompleteMail($memberId, Status::PRE_MEMBER_3); // E-mail the member
        }
        
        return false;
    }
    
/**
 * Warn a member.
 *
 * @param int $memberId The id of the member who we are warning.
 */
    private function __warnMember($memberId) {
		$memberDetails = $this->Member->recordWarning($memberId, $adminId);
		if ($memberDetails) {
           return $this->__sendMembershipRevokeMail($memberId, true); // E-mail the member
        }
        
        return false;
    }
    
/**
 * Revoke a membership.
 *
 * @param int $memberId The id of the member who we are approving.
 */
    private function __revokeMember($memberId, $adminId) {
		$memberDetails = $this->Member->revokeMembership($memberId, $adminId);
		if ($memberDetails) {
            return $this->__sendMembershipRevokeMail($memberId); // E-mail the member
		}
        
        return false;
    }
    
/**
 * Approve a membership.
 *
 * @param int $memberId The id of the member who we are approving.
 */
    private function __reinstateMember($memberId, $adminId) {
		$memberDetails = $this->Member->reinstateMembership($memberId, $adminId);
        if ($memberDetails) {
            return $this->__sendMembershipCompleteMail($memberId, Status::EX_MEMBER); // E-mail the member
        }
        
        return false;
    }
    
/**
 *
 * Send a "membership complete" e-mail to the member
 * @param int $id The id of the member to send to
 * @param int $statusId previous status_id of the member to email
 *
 */
	private function __sendMembershipCompleteMail($id, $statusId) {
        
        $memberDetails = $this->Member->getMemberSummaryForMember($id);
		$email = $this->Member->getEmailForMember($id);
		if ($email) {

			if ($statusId == Status::PRE_MEMBER_3) {
				$subject = 'Membership Complete';
				$template = 'to_member_access_details';
			} elseif ($statusId == Status::EX_MEMBER) {
				$subject = 'Your Membership Has Been Reinstated';
				$template = 'to_member_access_details_reinstated';
			}

			return $this->_sendEmail(
				$email,
				$subject,
				$template,
				array(
                    'name' => $memberDetails['bestName'],
                    'membersGuideHTML' => $this->Meta->getValueFor('members_guide_html'),
                    'membersGuidePDF' => $this->Meta->getValueFor('members_guide_pdf'),
                    'rulesHTML' => $this->Meta->getValueFor('rules_html'),
					'outerDoorCode' => $this->Meta->getValueFor('access_street_door'),
					'innerDoorCode' => $this->Meta->getValueFor('access_inner_door'),
					'wifiSsid' => $this->Meta->getValueFor('access_wifi_ssid'),
					'wifiPass' => $this->Meta->getValueFor('access_wifi_password'),
				)
			);
		}
        
        return false;
	}
    
/**
 *
 * Send a "membership revoke" ro warn e-mail to the member
 * @param int $id The id of the member to send to
 * @param int $statusId previous status_id of the member to email
 *
 */
	private function __sendMembershipRevokeMail($id, $warn = false) {
        $memberSoDetails = $this->Member->getSoDetails($id);
		if ($memberSoDetails != null) {
            if ($warn) {
                $subject = 'Your Membership May Be Revoked';
                $template = 'to_member_warn';
            } else {
                $subject = 'Your Membership Has Been Revoked';
                $template = 'to_member_revoked';
            }
            
			return $this->_sendEmail(
				$memberSoDetails['email'],
				$subject,
				$template,
				array(
					'name' => sprintf('%s %s', $memberSoDetails['firstname'], $memberSoDetails['surname']),
					'paymentRef' => $memberSoDetails['paymentRef'],
					'accountNum' => $this->Meta->getValueFor('so_accountNumber'),
					'sortCode' => $this->Meta->getValueFor('so_sortCode'),
					'accountName' => $this->Meta->getValueFor('so_accountName'),
				)
			);
		}
        
        return false;
    }
    
/**
 *
 * Send ohCrap list to software team
 * @param array $ohCrapIds List of Ids for members in states we need to manually fix
 */
	private function __sendSoftwareEmail($ohCrapIds) {
        $email = $this->Meta->getValueFor('software_team_email');
        $subject = 'HMS Audit issues';
        $template = 'notify_software_ohcrap';
    
        return $this->_sendEmail(
            $email,
            $subject,
            $template,
            array(
                  'ohCrapMembers' => $this->Member->getMemberSummaryForMembers($ohCrapIds),
            )
        );
    }

}