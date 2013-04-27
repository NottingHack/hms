<?php

	App::uses('Component', 'Controller');

	//! BankStatementComponent is a component to handle parsing of bank statement .csv files
	class BankStatementComponent extends Component 
	{
		public $components = array( 'Csv' ); //!< Needs the CsvComponent

		//! Attempt to read a bank statement .csv file
		/*!
			@param string $filePath The path to look for the file.
			@retval bool True if file was opened successfully and is bank statement csv, false otherwise.
		*/
		public function readFile($filePath)
		{
			if($this->Csv->readFile($filePath))
			{
				// If at-least one of the lines is a valid csv, assume it's ok
				$foundValidLine = false;

				for($i = 0; $i < $this->Csv->getNumLines(); $i++)
				{
					$transaction = $this->getLine($i);
					if(is_array($transaction))
					{
						$foundValidLine = true;
						break;
					}
				}

				return $foundValidLine;
			}

			return false;
		}

		//! Iterate over all transactions calling $callback for each valid transaction.
		/*!
			@param function $callback The function to call for each valid transaction.
		*/
		public function iterate($callback)
		{
			for($i = 0; $i < $this->Csv->getNumLines(); $i++)
			{
				$transaction = $this->getLine($i);
				if(is_array($transaction))
				{
					$callback(
						$transaction
					);
				}
			}
		}

		//! Get the bank statement line at index.
		/*!
			@param int $index The index of the line to get.
			@retval mixed Array of transaction data if index is in bounds and the line at index is a valid transaction, null otherwise.
		*/
		public function getLine($index)
		{
			$line = $this->Csv->getLine($index);

			if(	is_array($line) && 
				count($line) >= 7)
			{
				// Parse the date first
				$date = $this->_parseDate($line[0]);

				if($date == false || $date < 0)
				{
					// Invalid date
					return null;
				}

				// We don't really have anyway to validate the type...
				$type = $line[1];

				$parsedDesc = $this->_parseDesc($line[2]);

				$value = $line[3];
				if(!is_numeric($value))
				{
					return null;
				}

				$balance = $line[4];
				if(!is_numeric($balance))
				{
					return null;
				}

				$accountName = $line[5];
				$accountNum = $line[6];

				$result =  array(
					'date' => $date,
					'type' => $type,
					'value' => $value,
					'balance' => $balance,
				);

				$result = array_merge($result, $parsedDesc);

				return $result;
			}

			return null;
		}

		//! Parse the description field of the transaction.
		/*!
			@param string $desc The entire description field.
			@retval array An array of description data, may include Payee name, payment ref, FP id and transaction id, any of these fields may be null.
		*/ 
		private function _parseDesc($desc)
		{
			// Null all the values, we don't know how many we'll get
			$name = null;
			$ref = null;
			$fp = null;
			$id = null;

			$descArr = explode(',', $desc);

			$matchedfp = false;
			for($i = 0; $i < count($descArr); $i++)
			{
				$value = trim($descArr[$i]);
				if($i == 0)
				{
					$name = $value;
					continue;
				}

				$possibleFp = $this->_parseFp($value);
				if($possibleFp != null)
				{
					$fp = $possibleFp;
					$matchedfp = true;
				}
				else
				{
					// We assume that the reference comes before the FP
					if($matchedfp)
					{
						$id = $value;
					}
					else
					{
						$ref = $value;
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

		//! Parse the FP field of the transaction
		/*!
			@param $field The possible FP field.
			@retval Returns $field if it is a valid FP field, otherwise returns null.
		*/
		private function _parseFp($field)
		{
			$fieldArr = explode(' ', $field);

			if(	count($fieldArr) >= 2 &&
				$fieldArr[0] == "FP")
			{
				$date = $this->_parseDate($fieldArr[1]);

				if($date >= 0 && $date != false)
				{
					return $field;
				}
			}

			return null;
		}

		//! Attempt to parse a date from the bank statement
		/*!
			@param string $date Date to attempt to parse.
			@retval mixed If date can be parsed, returns that date as a unix epoch timestamp, otherwise returns false.
		*/
		private function _parseDate($date)
		{
			return strtotime(str_replace('/', '-', $date));
		}
	}

?>