<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable repeat purchase data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableRepeatPurchase extends AppModel 
	{
		public $useTable = 'consumable_repeat_purchases';	//!< Specify the table to use.
		public $primaryKey = 'repeat_purchase_id';			//!< Specify the promary key to use.

		//! We belong to a ConsumableArea
		public $belongsTo = array(
			'ConsumableArea' => array(
				'className' => 'ConsumableArea',
            	'foreignKey' => 'area_id'
			),
		);

		//! Validation rules.
	    public $validate = array(
	        'name' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Repeat purchase must have a name',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'min' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Repeat purchase must have a minimum value',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'max' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Repeat purchase must have a maximum value',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'area_id' => array(
	        	'numeric' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Area must be an existing area',
	        		'required' => true,
	        		'allowEmpty' => false,
	        	),
	        ),
	    );

		//! Add a new repeat purchase
		/*!
			@param array $data An array of data to create the repeat purchase.
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

		//! Get the repeat purchase information for a record.
		/*!
			@param int $id The if od the repeat purchase to get the information from.
			@retval array An array of repeat purchase data.
		*/
		public function get($id)
		{
			if(!is_numeric($id) || 
				$id <= 0)
			{
				throw new InvalidArgumentException('$id must be numeric and greater than zero');
			}

			$record = $this->findByRepeatPurchaseId($id);
			if(!is_array($record) || count($record) == 0)
			{
				return array();
			}

			return $this->_formatRecord($record);
		}

		//! Get the repeat purchase information for all records.
		/*!
			@retval array An array of repeat purchase data
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
			$formattedData = $record['ConsumableRepeatPurchase'];
			$formattedData['area'] = $record['ConsumableArea'];
			return $formattedData;
		}
	}
?>