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
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Model for all account data
 */
class BankTransaction extends AppModel {

/**
 * Specify the table to use
 * @var string
 */
	public $useTable = 'bank_transactions';

/**
 * Specify the primary key to use.
 * @var string
 */
	public $primaryKey = 'bank_transaction_id';	//!< Specify the primary key to use.
/**
 * Specify 'belongs to' associations.
 * @var array
 */
	public $belongsTo = array(
			'AccountBT' => array(
                'className' => 'Account',
                'foreignKey' => 'account_id',
			),
            'Bank' => array(
                'className' => 'Bank',
            ),
		);
/**
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'bank_transactions_id' => array(
			'content' => array(
				'rule' => 'numeric',
				'message' => 'Only numbers are allowed',
			),
			'length' => array(
				'rule' => array('between', 1, 11),
				'message' => 'Number must be between 1 and 11 characters long',
			),
		),
        'description' => array(
            'content' => array(
                'rule' => 'notBlank',
                'message' => 'Must have a description',
            ),
            'unique' => array(
              'rule' => array('isUnique', array('description', 'transaction_date', 'amount'), false),
                'message' => 'This record already exists.'
            ),
		),
		'amount' => array(
			'content' => array(
				'rule' => array('decimal', 2),
				'message' => 'Only numbers are allowed',
			),
		),
		'account_id' => array(
			'length' => array(
				'rule' => array('between', 1, 11),
				'message' => 'Account id must be between 1 and 12 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'Account id must be a number',
			),
		),
        'transaction_date' => array(
            'rule' => 'date',
            'allowEmpty' => false,
            'message' => 'Must have a valid date'
        ),
	);
    
/**
 * Format an array of bank transactions.
 * 
 * @param array $bankTransactionList The array of bank transactions.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array An array of formatted member infos.
 */
	public function formatBankTransactionList($bankTransactionList, $removeNullEntries) {
		$formattedInfos = array();
		foreach ($bankTransactionList as $transactionInfo) {
			array_push($formattedInfos, $this->formatBankTransactionInfo($transactionInfo, $removeNullEntries));
		}
		return $formattedInfos;
	}

/**
 * Format bank transaction information into a nicer arrangement.
 * 
 * @param array $bankTransactionInfo The info to format, usually retrieved from BankTransaction::getBankTransactionList.
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @return array An array of bank transaction information, formatted so that nothing needs to know database rows.
 * @link BankTransaction::getBankTransactionList
 * @link
 */
	public function formatBankTransactionInfo($bankTransactionInfo, $removeNullEntries) {
		$id = Hash::get($bankTransactionInfo, 'BankTransaction.bank_transaction_id');
        $transaction_date = Hash::get($bankTransactionInfo, 'BankTransaction.transaction_date');
		$description = Hash::get($bankTransactionInfo, 'BankTransaction.description');
		$amount = Hash::get($bankTransactionInfo, 'BankTransaction.amount');
        $bank = Hash::get($bankTransactionInfo, 'Bank.name');
		$account = array();
		if (array_key_exists('AccountBT', $bankTransactionInfo)) {
			$account['id'] = Hash::get($bankTransactionInfo, 'AccountBT.account_id');
			$account['payment_ref'] = Hash::get($bankTransactionInfo, 'AccountBT.payment_ref');
		}

		$allValues = array(
			'id' => $id,
            'transaction_date' => $transaction_date,
            'description' => $description,
            'amount' => $amount,
            'bank' => $bank,
            'account' => $account,
		);

		if (!$removeNullEntries) {
			return $allValues;
		}

		// Filter out any values that are null or false etc.
		$onlyValidValues = array();

		foreach ($allValues as $key => $value) {
			if (isset($value) != false) {
				if (is_array($value) && empty($value)) {
					continue;
				}
				$onlyValidValues[$key] = $value;
			}
		}
        
        // remove account if null
        if (!$onlyValidValues['account']['id']) {
            unset($onlyValidValues['account']);
        }
        
		return $onlyValidValues;
	}


/**
 * Get a list of transactions for a given Account
 * 
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @param bool $format If true format the data first, otherwise just return it in the same format as the datasource gives it us.
 * @return array A list of transactions or query to reports a list of transactions
 */
	public function getBankTransactionList($paginate, $conditions = array(), $format = true) {
		$findOptions = array(
			'conditions' => $conditions,
            'order' => 'BankTransaction.transaction_date DESC'
		);
		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );
        
        if ($format) {
            return $this->formatBankTransactionList($info, false);
        }
        
		return $info;
	}

/**
 * Get last transaction on record for a member
 *
 * @param int $accountId
 * @param bool $format If true format the data first, otherwise just return it in the same format as the datasource gives it us.
 * @return string
 **/
    public function getLastTransactionForAccount($accountId, $format = true) {
        $options = array(
            'conditions' => array('BankTransaction.account_id' => $accountId),
            'order' => 'BankTransaction.transaction_date DESC'
            );

        $lastTransaction = $this->find('first', $options);
        // $lastTransaction = Hash::get($result, 'BankTransaction.transaction_date');
        if ($format) {
            return $this->formatBankTransactionInfo($lastTransaction, false);
        }
        return $lastTransaction;
    }
/**
 * Proces
 *
 * Incoming array expects the following format for each transaction
 * array(
 *
 * @param array $transactions array of transactions we need to add to the db
 * @return int Import results
 */
    public function importTransactions($transactions) {
        $result = $this->saveMany($transactions,
                                  $options = array(
                                                   'validate' => true,
                                                   'atomic' => false,
                                                   'fieldList' => array(
                                                                        'transaction_date',
                                                                        'description',
                                                                        'amount',
                                                                        'bank_id',
                                                                        'account_id',
                                                                        )
                                                   )
                                  );

        return $result;
    }
    
}
