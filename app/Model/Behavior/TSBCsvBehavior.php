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
 * @package       app.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


App::uses('ModelBehavior', 'Model');
App::uses('CakeTime', 'Utility');

/**
 * A wrapper around a CsvReader to make it a Behavior that can be
 * used by Models.
 *
 * @package app.Model.Behavoir
 */
class TSBCsvBehavior extends ModelBehavior {

/**
 * Perform initial setup.
 *
 * @param Model $model The model we're being attached to.
 * @param array $settings Any settings passed from the model.
 * @link http://book.cakephp.org/2.0/en/models/behaviors.html#creating-a-behavior-callback
 */
	public function setup(Model $model, $settings = array()) {
        // we need to use the Account model later
        $this->Account = ClassRegistry::init('Account');
	}
    
/**
 * afterFind Callback
 * Use this callback to reformat the data to a shape that matches what BankTransaction Models importTransactions function expects
 *
 * @param Model $Model Model find was run on
 * @param array $results Array of model results.
 * @param bool $primary Did the find originate on $model.
 * @return array Modified results
 */
    public function afterFind(Model $model, $results, $primary = false) {
        $formattedTransactions = array();
        foreach($results as $transaction) {
            array_push($formattedTransactions, $this->__formatTransaction($transaction['CsvUpload'], true, false));
        }
        
        // get account_id's in bulk
        $refsToMatch = array();
        foreach ($formattedTransactions as $transaction) {
            if (isset($transaction['ref'])) {
                array_push($refsToMatch, $transaction['ref']);
            }
        }
        $accountIds = $this->Account->getAccountIdsForRefs($refsToMatch);
        
        $withIds = array();
        foreach ($formattedTransactions as $transaction) {
            if (isset($transaction['ref']) && isset($accountIds[$transaction['ref']])) {
                $transaction['account_id'] = $accountIds[$transaction['ref']];
                unset($transaction['ref']);
            }
            array_push($withIds, $transaction);
        }
        
        return $withIds;
    }
    
/**
 * Format Transaction 
 *
 * @param array $transaction The transaction to format as retrieved from a CSV record
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array.
 * @param bool $getAccountID If true we got off to Account and look for the ref match and return account_id, else we return the $ref
 * @return array An array of member information, formatted so that nothing needs to know database rows.
 */
    private function __formatTransaction($transaction, $removeNullEntries, $getAccountId = true) {
        /*  incoming transaction
         array(
            'Transaction Date' => '03/02/2016',
            'Transaction Type' => 'FPI',
            'Sort Code' => ''77-22-24',
            'Account Number' => '13007568',
            'Transaction Description' => 'DUCKHOUSE J  V02 HSNTSBQ7XBJ38YDQ 51023332313523000N 560061     30 03FEB16 02:51 ',
            'Debit Amount' => '',
            'Credit Amount' => '20.00',
            'Balance' => '18685.05'
        )
        */
        
        if ($transaction['Debit Amount'] != '') {
            $amount = '-' . $transaction['Debit Amount'];
        } else {
            $amount = $transaction['Credit Amount'];
        }
        
        // now to regex the description filed and try to match out a HSNTSB ref
        $account_id = null;
        $ref = null;
        $pattern = '/HSNTSB.*?(?=\s)/';
        if (preg_match($pattern, $transaction['Transaction Description'], $matches) == 1) {
            if ($getAccountId) {
                $accountIds = $this->Account->getAccountIdsForRefs($matches);
                if (isset($accountIds[$matches[0]])) {
                    $account_id = $accountIds[$matches[0]];
                }
                
            } else {
                 $ref = $matches[0];
            }
        }
        
        $allValues = array(
			'transaction_date' => CakeTime::format($transaction['Transaction Date'], '%Y-%m-%d'),
			'description' => $transaction['Transaction Description'],
			'amount' => $amount,
			'account_id' => $account_id,
            'ref' => $ref,
            'bank_id' => '2',                     // Needs to match banks table entry
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
        
        return $onlyValidValues;
    }
    
}