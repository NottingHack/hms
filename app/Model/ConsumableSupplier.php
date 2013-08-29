<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable supplier data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableSupplier extends AppModel 
	{
		public $useTable = 'consumable_suppliers';	//!< Specify the table to use.
		public $primaryKey = 'supplier_id';			//!< Specify the promary key to use.

		//! Validation rules.
	    public $validate = array(
	        'name' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Supplier must have at-least a name',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	    );

		//! Add a new supplier
		/*!
			@param array $data An array of data to create the supplier.
			@retval bool True if record was created successfully, false otherwise.
		*/
		public function add($data)
		{
			if(!is_array($data))
			{
				throw new InvalidArgumentException('$data must be of type array');
			}

			$this->create($data);
			if(!$this->validates())
			{
				throw new InvalidArgumentException('Information in $data did not correspond with validation rules');
			}

			return (bool)$this->save($data);
		}

		//! Get the supplier information for a record.
		/*!
			@param int $id The if od the supplier to get the information from.
			@retval array An array of supplier data.
		*/
		public function get($id)
		{
			if(!is_numeric($id) || 
				$id <= 0)
			{
				throw new InvalidArgumentException('$id must be numeric and greater than zero');
			}

			$record = $this->findBySupplierId($id);
			if(!is_array($record) || count($record) == 0)
			{
				return array();
			}

			return $this->_formatRecord($record);
		}

		//! Get the supplier information for all records.
		/*!
			@retval array An array of supplier data
		*/
		public function getAll()
		{
			$formattedRecords = array();
			foreach ($this->find('all') as $index => $record) 
			{
				array_push($formattedRecords, $this->_formatRecord($record));
			}
			return $formattedRecords;
		}

		//! Format a record for use outside of this class
		/*!
			@param array $record The data to be formatted.
			@retval array The formatted data.
		*/
		private function _formatRecord($record)
		{
			// Just need to remove the model name, for now.
			$formattedData = $record['ConsumableSupplier'];
			return $formattedData;
		}
	}
?>