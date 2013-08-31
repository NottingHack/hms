<?php

	App::uses('AppModel', 'Model');

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

		/*! 
			We have a ConsubableRequestStatus
			Also have a ConsumableSupplier (but that may be null)
			Also have a ConsumableArea
			May have a ConsumableRepeatPurchase if this request was spawned from a repeat purchase
		*/
		public $hasOne = array(
			'ConsumableRequestStatus',
			'ConsumableSupplier',
			'ConsumableArea',
			'ConsumableRepeatPurchase',
		);

		/*!
			We have many ConsumableRequestComments
		*/
		public $hasMany = array(
			'ConsumableRequestComment',
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
	}
?>