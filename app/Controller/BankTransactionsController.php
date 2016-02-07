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
App::uses('PhpReader', 'Configure');
Configure::config('default', new PhpReader());
Configure::load('hms', 'default');
    
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
 * Test to see if a user is authorised to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorised to make the request, otherwise false.
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
 * Present an empty index page
 *
 */
    public function index() {
        // nothing to see here
    }
   
/**
 * Upload a .csv file of bank transactions and look for members to approve.
 *
 * @param string $guid If set then look here in the session for a list of account id's to approve.
 */
    public function uploadCsv() {
        if ($this->request->is('post')) {
            $data = $this->request->data;

            // did we actually get a file
            if (!Hash::get($data, 'filename.tmp_name')) {
                // didn't get a filename
                $this->Session->setFlash('No file uploaded.');
                return;
            }
            // does it have .csv extension (or is mimeType text/csv
            if (Hash::get($data, 'filename.type') != "text/csv") {
                $this->Session->setFlash('File is not a CSV type');
                return;
            }
            // move file to csv folder
            $tmp_name = Hash::get($data, 'filename.tmp_name');
            $filename = Hash::get($data, 'filename.name');
            $dir = Configure::read('hms_csv_folder');
            
            // has this file already been uploaded if so
            if (file_exists("$dir/$filename")) {
                $this->Session->setFlash("A file with this name has already been previously uploaded");
                return;
            }
            
            move_uploaded_file($tmp_name, "$dir/$filename");
            
            // strip .csv from file name before passing to classRegistry
            $filename = preg_replace('/.csv$/', '', $filename);
            
            // load CsvUpload model
            $this->CsvUpload = ClassRegistry::init(array(
                                      'class' => 'CsvUpload',
                                      'alias' => 'CsvUpload',
                                      'table' => $filename
                                      ));
            
            // read csv and get a nice formatted list of new transactions
            $transactions = $this->CsvUpload->find('all');
            
            if (count($transactions) == 0) {
                $this->Session->setFlash("No transactions found in CSV file");
                return;
            }
            

            // ok looks good like we have something pass it on to the model to import
            $ret = $this->BankTransaction->importTransactions($transactions);
            
            // email accounts with list a transaction that could not be matched to accounts, some might just be other xfers, some might be special people)
            // TODO:
            
            
            // place holder for returns
            $this->Session->setFlash("CSV upload complete");
            return;
        }
        
    }

/**
 * Show a list of all transactions for $memberId, or for the logged in member if $memberId isn't set.
 * @param int|null $memberId The members id to list all transactions for
 */
	public function history($memberId = null) {

		if ($memberId == null) {
			$memberId = $this->_getLoggedInMemberId();
		}
        // grab account_id from member_id
        $this->Member->id = $memberId;
        $accountId = $this->Member->field('account_id');
        
        $this->__paginateTransactionList($accountId);

	}

/**
 * Paginate a list of all transactions for a given member
 *
 * @param int $memberId The members id to list all transactions for
 */
	private function __paginateTransactionList($accountId) {
		$this->paginate = $this->BankTransaction->getBankTransactionList(true, array('AccountBT.account_id' => $accountId));
		$bankTransactionsList = $this->paginate('BankTransaction');
        $bankTransactionsList = $this->BankTransaction->formatBankTransactionList($bankTransactionsList, false);
 		$this->set('bankTransactionsList', $bankTransactionsList);
	}

}