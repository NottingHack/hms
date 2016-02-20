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
	public $uses = array('Member', 'BankTransaction');

/**
 * Constanst to define when membership is remove and warned
 */
    const REVOKE_INTERVAL = "P2M";
    const WARN_INTERVAL = "P1M14D";

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
        $reactivateIds = array();
        $ohCrapIds = array();

        $dateNow = new DateTime(); // this will be the server time the we run, might need to shift time portion to end of the day 23:59
        $dateNow->setTime(0,0,0);
        $warnDate = clone $dateNow;
        $warnDate->sub(new DateInterval(self::WARN_INTERVAL));
        $revokeDate = clone $dateNow;
        $revokeDate->sub(new DateInterval(self::REVOKE_INTERVAL));

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
                            // reactivate member
                            array_push($reactivateIds, $memberId);
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
        
        debug($approveIds);
        debug($warnIds);
        debug($revokeIds);
        debug($reactivateIds);
        debug($ohCrapIds);
              

        
        $this->Session->setFlash('No audit ran');
        
        return; //$this->redirect(array( 'controller' => 'members'));
        
    }

}