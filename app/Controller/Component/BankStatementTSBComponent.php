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
 * @package       app.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');

/**
 * BankStatementComponent is a component to handle parsing of TSB bank statement .csv files.
 */
class BankStatementTSBComponent extends Component {

/**
 * List of components this component uses.
 * @var array
 */
	public $components = array( 'Csv' );

/**
 * Attempt to read a bank statement .csv file.
 *
 * @param string $filePath The path to look for the file.
 * @return bool True if file was opened successfully and is bank statement csv, false otherwise.
 */
	public function readFile($filePath) {
		if ($this->Csv->readFile($filePath)) {
			// If at-least one of the lines is a valid csv, assume it's ok
			$foundValidLine = false;

			$numLines = $this->Csv->getNumLines();
			for ($i = 0; $i < $numLines; $i++) {
				$transaction = $this->getLine($i);
				if (is_array($transaction)) {
					$foundValidLine = true;
					break;
				}
			}

			return $foundValidLine;
		}

		return false;
	}

/** 
 * Iterate over all transactions calling $callback for each valid transaction.
 * 
 * @param function $callback The function to call for each valid transaction.
 */
	public function iterate($callback) {
		$numLines = $this->Csv->getNumLines();
		for ($i = 0; $i < $numLines; $i++) {
			$transaction = $this->getLine($i);
			if (is_array($transaction)) {
				$callback(
					$transaction
				);
			}
		}
	}

/** 
 * 
 * Get the bank statement line at index.
 *
 *	@param int $index The index of the line to get.
 *	@return mixed Array of transaction data if index is in bounds and the line at index is a valid transaction, null otherwise.
 */
	public function getLine($index) {
		$line = $this->Csv->getLine($index);
        
        if (is_array($line) && count($line) >= 7) {
			// Parse the date first
			$date = $this->__parseDate($line[0]);

			if ($date == false || $date < 0) {
				// Invalid date
				return null;
			}

			// We don't really have anyway to validate the type...
			$type = $line[1];

            $ref = null;
            $pattern = '/HSNTSB.*?(?=\s)/';
            if (preg_match($pattern, $line[4], $matches) == 1) {
                $ref = $matches[0];
            }
            
            if ($line['5'] != '') {
                $value = '-' . $line[5];
            } else {
                $value = $line[6];
            }
            if (!is_numeric($value)) {
				return null;
			}

			$balance = $line[7];
			if (!is_numeric($balance)) {
				return null;
			}

			$result = array(
				'date' => $date,
				'type' => $type,
				'value' => $value,
				'balance' => $balance,
                'ref' => $ref,
			);
            
            return $result;
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