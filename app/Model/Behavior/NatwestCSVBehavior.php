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
class NatwestCsvBehavior extends ModelBehavior {
    
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
        // remove the first entry
        unset($results[0]); // remove item at index 0
        $results = array_values($results); // 'reindex' array
        
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
        $accountIds = $this->Account->getAccountIdsForNatwestRefs($refsToMatch);
        
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
 * @param array $transaction The transition to format as retrieved from a CSV record
 * @param bool $removeNullEntries If true then entries that have a value of null, false or an empty array won't exist in the final array. 
 * @param bool $getAccountID If true we got off to Account and look for the ref match and return account_id, else we return the $ref
 * @return array An array of member information, formatted so that nothing needs to know database rows.
 */
    private function __formatTransaction($transaction, $removeNullEntries, $getAccountId = true) {
        /*  incoming transaction
         array(
			'Date' => '02/12/2015',
			' Type' => 'BAC',
			' Description' => ''MR O.MEARA , HACKSPACE BO.M , FP 02/12/15 0215 , 000000000053043693',
			' Value' => '5.00',
			' Balance' => '2537.14',
			' Account Name' => ''NOTTINGHACK',
			' Account Number' => ''602477-19098596'
         )
        */
        
        // now to regex the description filed and try to match out a HSNTSB ref
        $account_id = null;
        $ref = null;
        $parsedDesc = $this->__parseDesc($transaction[' Description']);
        
        $pattern = '/^HSNOTTS/';
        if (preg_match($pattern, $parsedDesc['ref']) != 1) {
            // we didn't get something with HSNOTTS in it, use a combined (name , ref) limited to 18 chars
            $newRef = $parsedDesc['name'] . ' , ' . $parsedDesc['ref'];
            $ref = substr($newRef, 0, 18);
        } else {
            $ref = $parsedDesc['ref'];
        }
        
        if ($getAccountId) {
            $accountIds = $this->Account->getAccountIdsForNatwestRefs(array($ref));
            if (isset($accountIds[0])) {
                $account_id = $accountIds[0];
            }
            $ref = null;
        }
        
        // make PHP treat it as a UK date not US http://stackoverflow.com/a/2891949
        $transaction['Date'] = str_replace('/', '-', $transaction['Date']);
        
        $allValues = array(
			'transaction_date' => CakeTime::format($transaction['Date'], '%Y-%m-%d'),
			'description' => $transaction[' Description'],
			'amount' => $transaction[' Value'],
			'account_id' => $account_id,
            'ref' => $ref,
            'bank_id' => '1',                     // Needs to match banks table entry
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
    
/**
 * Parse the description field of the transaction.
 *
 * @param string $desc The entire description field.
 * @return array An array of description data, may include Payee name, payment ref, FP id and transaction id, any of these fields may be null.
 */
	private function __parseDesc($desc) {
		// Null all the values, we don't know how many we'll get
		$name = null;
		$ref = null;
		$fp = null;
		$id = null;

		$descArr = explode(',', $desc);

		$matchedfp = false;
		$numDescParts = count($descArr);
		for ($i = 0; $i < $numDescParts; $i++) {
			$value = trim($descArr[$i]);
			if ($i == 0) {
				$name = trim($value, "'");
				continue;
			}

			$possibleFp = $this->__parseFp($value);
			if ($possibleFp != null) {
				$fp = $possibleFp;
				$matchedfp = true;
			} else {
				// We assume that the reference comes before the FP
				if ($matchedfp) {
					$id = $value;
				} else {
                    $ref = substr($value, 0, 18);   // limit to only 18 chars worth
				}
			}
		}

		return array(
			'name' => $name,
			'ref' => $ref,
			'fp' => $fp,
			'id' => $id,
		);
	}

/** 
 * Parse the FP field of the transaction
 *
 *	@param $field The possible FP field.
 *	@return Returns $field if it is a valid FP field, otherwise returns null.
 */
	private function __parseFp($field) {
		$fieldArr = explode(' ', $field);

		if (count($fieldArr) >= 2 && $fieldArr[0] == "FP") {
			$date = $this->__parseDate($fieldArr[1]);

			if ($date >= 0 && $date != false) {
				return $field;
			}
		}

		return null;
	}


/** 
 * Attempt to parse a date from the bank statement
 *
 *	@param string $date Date to attempt to parse.
 *	@return mixed If date can be parsed, returns that date as a unix epoch timestamp, otherwise returns false.
 */
	private function __parseDate($date) {
		return strtotime(str_replace('/', '-', $date));
	}

}