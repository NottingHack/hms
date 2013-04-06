<?php

	App::uses('CsvComponent', 'Controller/Component');

	//! BankStatementComponent is a component to handle parsing of bank statement .csv files
	class BankStatementComponent extends CsvComponent 
	{
		//! Get the bank statement line at index.
		/*!
			@param int $index The index of the line to get.
			@retval mixed Array of transaction data if index is in bounds and the line at index is a valid transaction, null otherwise.
		*/
		public function getLine($index)
		{
			$line = parent::getLine($index);

			if(	is_array($line) && 
				count($line) >= 7)
			{
				// Parse the date first
				$date = strtotime($line[0]);

				if($date == false || $date < 0)
				{
					// Invalid date
					return null;
				}

				// We don't really have anyway to validate the type...
				$type = $line[1];

				// Description may be up to 4 items, which for some reason
				// aren't specified as different .csv fields...
				$descArr = explode(',', $line[2]);
				if(count($descArr) <= 0)
				{
					return null;
				}

				$parsedDesc = $this->_parseDesc($descArr);

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

		private function _parseDesc($descArr)
		{
			// Null all the values, we don't know how many we'll get
			$name = null;
			$ref = null;
			$fp = null;
			$id = null;

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

		private function _parseFp($value)
		{
			$valArr = explode(' ', $value);

			if(	count($valArr) >= 2 &&
				$valArr[0] == "FP")
			{
				$date = strtotime($valArr[1]);

				if($date >= 0 && $date != false)
				{
					return $value;
				}
			}

			return null;
		}
	}

?>