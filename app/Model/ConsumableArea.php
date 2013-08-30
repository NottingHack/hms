<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable area data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableArea extends AppModel 
	{
		public $useTable = 'consumable_areas';	//!< Specify the table to use.
		public $primaryKey = 'area_id';			//!< Specify the primary key to use.

		//! Validation rules.
	    public $validate = array(
	        'name' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Area must have at-least a name',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	    );

	    //! We haveMany ConsumableRepeatPurchase
		public $hasMany = array(
			'ConsumableRepeatPurchase' => array(
				'className' => 'ConsumableRepeatPurchase',
            	'foreignKey' => 'area_id'
			),
		);

		//! Add a new area
		/*!
			@param array $data An array of data to create the area.
			@retval bool True if record was created successfully, false otherwise.
		*/
		public function add($data)
		{
			$this->create($data);
			if(!$this->validates())
			{
				throw new InvalidArgumentException('Information in $data did not correspond with validation rules');
			}

			return (bool)$this->save($data);
		}

		//! Get the area information for a record.
		/*!
			@param int $id The if od the area to get the information from.
			@retval array An array of area data.
		*/
		public function get($id)
		{
			if(!is_numeric($id) || 
				$id <= 0)
			{
				throw new InvalidArgumentException('$id must be numeric and greater than zero');
			}

			$record = $this->findByAreaId($id);
			if(!is_array($record) || count($record) == 0)
			{
				return array();
			}

			return $this->_formatRecord($record);
		}

		//! Get the area information for all records.
		/*!
			@retval array An array of area data
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
			$formattedData = $record['ConsumableArea'];
			$formattedData['repeatPurchases'] = $record['ConsumableRepeatPurchase'];
			return $formattedData;
		}
	}
?>