<?php

	App::uses('Component', 'Controller');

	//! CsvComponent is a component to handle both uploading and getting data from .csv files
	class CsvComponent extends Component 
	{
		var $lines = array();	//!< Array of lines found in the file

		//! Attempt to read a .csv file
		/*!
			@param string $filePath The path to look for the file.
			@retval bool True if file was opened successfully, false otherwise.
		*/
		public function readFile($filePath)
		{
			$fileHandle = fopen($filePath, 'r');

			if($fileHandle == 0)
			{
				return false;
			}

			$this->lines = array();

			while (($data = fgetcsv($fileHandle)) !== FALSE) 
			{
				// Ignore blank lines
				if($data != null)
				{
					array_push($this->lines, $data);
				}
			}

			fclose($fileHandle);

			return true;
		}

		//! Get the number of lines available.
		/*!
			@retval int The number of lines available.
		*/
		public function getNumLines()
		{
			return count($this->lines);
		}

		//! Get the line at index.
		/*!
			@param int $index The index of the line to retrieve.
			@retval mixed An array of line data if index is valid, otherwise null.
		*/
		public function getLine($index)
		{
			if(	$index >= 0 &&
				$index < $this->getNumLines())
			{
				return $this->lines[$index];
			}

			return null;
		}
	}

?>