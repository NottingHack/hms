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
 * @package       dev.Setup.Common
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Class that handles writing runtime data to SQL format.
 */
class SqlWriter {

/**
 * Given a value, return an SQL version of that value. 
 * @param  mixed $value The value to write.
 * @return string SQL string representation of the value.
 */
	public function write($value) {
		if (is_numeric($value)) {
			return strval($value);
		}

		if (is_string($value)) {
			// Note that this escaping is not secure
			// but it's about the best we can do without connecting
			// to a database.
			return "'" . str_replace("'", "\'", $value) . "'";
		}

		return 'NULL';
	}

/**
 * Given an name and an array of data, return the SQL code needed
 * to insert that data into the named table.
 * @param  string $name Name of the table.
 * @param  array $data An associative array of data.
 * @return string|null The SQL code needed to insert data into the table, or null on error.
 */
	public function writeInsert($name, $data) {
		if (!is_array($data) || count($data) <= 0) {
			return null;
		}

		// Assume that the first 'row' of the data contains all the columns
		$headers = array_keys($data[0]);

		$sqlHeaders = array_map( function ($val) {
			return "`$val`";
		}, $headers);

		$sql = "INSERT INTO `$name` (";
		$sql .= implode(', ', $sqlHeaders);
		$sql .= ") VALUES" . PHP_EOL;

		$numItems = count($data);
		for ($i = 0; $i < $numItems; $i++) {
			$values = $data[$i];

			$slqValues = array_map( function ($val) {
				return $this->write($val);
			}, $values);

			$sql .= "(" . implode(', ', $slqValues) . ")";

			if ($i < $numItems - 1) {
				$sql .= ',';
			} else {
				$sql .= ';';
			}
			$sql .= PHP_EOL;
		}

		return $sql;
	}
}