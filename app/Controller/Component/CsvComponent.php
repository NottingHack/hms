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
App::import('Lib/CsvReader', 'CsvReader');

/**
 * A wrapper around a CsvReader to make it a Component that can be
 * used by Controllers or other Components.
 *
 * @package app.Controller.Component
 */
class CsvComponent extends Component {

/**
 * Internal CsvReader that actually does all the work.
 * @var CsvReader
 */
	private $__csvReader;

/**
 * Constructor
 *
 * @param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components.
 * @param array $settings Array of configuration settings.
 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);

		$this->__csvReader = new CsvReader();
	}

/**
 * Attempt to read a .csv file
 *
 * @param string $filePath The path to look for the file.
 * @return bool True if file was opened successfully, false otherwise.
 */
	public function readFile($filePath) {
		return $this->__csvReader->readFile($filePath);
	}

/**
 * Get the number of lines available.
 *
 * @return int The number of lines available.
 */
	public function getNumLines() {
		return $this->__csvReader->getNumLines();
	}

/**
 * Get the line at index.
 *
 * @param int $index The index of the line to retrieve.
 * @return mixed An array of line data if index is valid, otherwise null.
 */
	public function getLine($index) {
		return $this->__csvReader->getLine($index);
	}
}