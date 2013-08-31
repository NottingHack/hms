<?php

	App::uses('AppModel', 'Model');
	App::uses('ConsumableRequestStatus', 'Model');

	/**
	 * Model for all consumable request data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableRequest extends AppModel 
	{
		public $useTable = 'consumable_requests';	//!< Specify the table to use.
		public $primaryKey = 'request_id';			//!< Specify the promary key to use.


		public $belongsTo = array(
			'ConsumableRequestStatus' => array(
				'className' => 'ConsumableRequestStatus',
            	'foreignKey' => 'request_status_id'
			),
			'ConsumableSupplier' => array(
				'className' => 'ConsumableSupplier',
            	'foreignKey' => 'supplier_id'
			),
			'ConsumableArea' => array(
				'className' => 'ConsumableArea',
            	'foreignKey' => 'area_id'
			),
			'ConsumableRepeatPurchase' => array(
				'className' => 'ConsumableRepeatPurchase',
            	'foreignKey' => 'repeat_purchase_id'
			),
		);

		public $hasMany = array(
			'ConsumableRequestComment' => array(
				'className' => 'ConsumableRequestComment',
            	'foreignKey' => 'request_id'
			),
		);

		//! Validation rules.
	    public $validate = array(
	        'title' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Request must have a title',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'detail' => array(
	            'notEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'Request must have a detailed description',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'request_status_id' => array(
	            'number' => array(
	            	'rule' => array('naturalNumber', false),
	        		'message' => 'Request must have a valid status',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'supplier_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Supplier must be valid',
	        		'required' => false,
	        		'allowEmpty' => true,
	        	),
	        ),
	        'area_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Area must be valid',
	        		'required' => false,
	        		'allowEmpty' => true,
	        	),
	        ),
	        'repeat_purchase_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Repeat Purchase must be valid',
	        		'required' => false,
	        		'allowEmpty' => true,
	        	),
	        ),
	    );

		//! Add a new area
		/*!
			@param array $data An array of data to create the area.
			@retval bool True if record was created successfully, false otherwise.
		*/
		public function add($data)
		{
			// The status of a request being added must be 'Pending'
			if(	is_array($data) && 
				array_key_exists('ConsumableRequest', $data) &&
				is_array($data['ConsumableRequest']) )
			{
				$data['ConsumableRequest']['request_status_id'] = ConsumableRequestStatus::PENDING;
			}
			
			$this->create($data);
			if(!$this->validates())
			{
				throw new InvalidArgumentException('Information in $data did not correspond with validation rules');
			}

			return (bool)$this->save($data);
		}
	}
?>