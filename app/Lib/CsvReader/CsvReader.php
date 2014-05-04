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
 * @package       app.Lib.CsvReader
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


/**
 * Class to handle reading CSV files. Warning, reads entire file in to memory.
 */
class CsvReader {

/**
 * Array of lines found in the file.
 * @var array
 */
	private $__lines = array();

/**
 * Attempt to read a .csv file
 * 
 * @param string $filePath The path to look for the file.
 * @return bool True if file was opened successfully, false otherwise.
 */
	public function readFile($filePath) {
		if (!is_string($filePath)) {
			return false;
		}

		$fileHandle = fopen($filePath, 'r');

		if ($fileHandle == 0) {
			return false;
		}

		$this->__lines = array();

		while (($data = fgetcsv($fileHandle)) !== false) {
			// Ignore blank lines
			if ($data != null) {
				array_push($this->__lines, $data);
			}
		}

		if (count($this->__lines) <= 0) {
			return false;
		}

		fclose($fileHandle);

		return true;
	}

/**
 * Get the number of __lines available.
 * 
 * @return int The number of __lines available.
 */
	public function getNumLines() {
		return count($this->__lines);
	}

/**
 * Get the line at index.
 *
 * @param int $index The index of the line to retrieve.
 * @return mixed An array of line data if index is valid, otherwise null.
 */
	public function getLine($index) {
		if ($index >= 0 && $index < $this->getNumLines()) {
			return $this->__lines[$index];
		}

		return null;
	}
}