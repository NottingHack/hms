<?php

	App::uses('Component', 'Controller');
	App::import('Lib/CsvReader', 'CsvReader');

	//! CsvComponent is a component to handle both uploading and getting data from .csv files
	class CsvComponent extends Component 
	{
		private $csvReader; //!< Calls to work with the .csv file are redirected to this CsvReader.

		//! Constructor
		/*!
			@param ComponentCollection $collection A ComponentCollection this component can use to lazy load its components
 			@param array $settings Array of configuration settings.
 		*/
		public function __construct(ComponentCollection $collection, $settings = array()) 
		{
			parent::__construct($collection, $settings);

			$this->csvReader = new CsvReader();
		}

		//! Attempt to read a .csv file
		/*!
			@param string $filePath The path to look for the file.
			@retval bool True if file was opened successfully, false otherwise.
		*/
		public function readFile($filePath)
		{
			return $this->csvReader->readFile($filePath);
		}

		//! Get the number of lines available.
		/*!
			@retval int The number of lines available.
		*/
		public function getNumLines()
		{
			return $this->csvReader->getNumLines();
		}

		//! Get the line at index.
		/*!
			@param int $index The index of the line to retrieve.
			@retval mixed An array of line data if index is valid, otherwise null.
		*/
		public function getLine($index)
		{
			return $this->csvReader->getLine($index);
		}
	}

?>