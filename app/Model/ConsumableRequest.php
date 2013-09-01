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
			'Member' => array(
				'className' => 'Member',
				'foreignKey' => 'member_id',
				'fields' => array( 'member_id', 'username' ),
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
	        'member_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Member must be valid',
	        		'required' => false,
	        		'allowEmpty' => true,
	        	),
	        ),
	    );

		//! Add a new request
		/*!
			@param array $data An array of data to create the request.
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

		//! Add a new request from a repeat purchase
		/*!
			@param int $repeatPurchaseId The id of the repeat purchase to use to create the request.
			@retval bool True if records was created successfully, false otherwise.
		*/
		public function addFromRepeatPurchase($repeatPurchaseId)
		{
			if( !( is_numeric($repeatPurchaseId) &&
					$repeatPurchaseId > 0 ) )
			{
				throw new InvalidArgumentException('$repeatPurchaseId must be a number greater that zero');
			}

			$repeatPurchaseRecord = $this->ConsumableRepeatPurchase->findByRepeatPurchaseId($repeatPurchaseId);
			if(!is_array($repeatPurchaseRecord) || count($repeatPurchaseRecord) == 0)
			{
				throw new InvalidArgumentException('$repeatPurchaseId must be the id of an actual repeat purchase');
			}

			$addData = array(
				'ConsumableRequest' => array(
					'title' => $repeatPurchaseRecord['ConsumableRepeatPurchase']['name'],
					'detail' => $this->_getRequestDetailFromRepeatPurchaseData($repeatPurchaseRecord),
					'url' => null,
					'supplier_id' => $this->_getLsatSupplierForRepeatPurchase($repeatPurchaseId),
					'area_id' => $repeatPurchaseRecord['ConsumableRepeatPurchase']['area_id'],
					'repeat_purchase_id' => $repeatPurchaseId,
					'member_id' => null,
				),
			);

			return $this->add($addData);
		}

		//! Given an array of repeat purchase data, return a string for use in the 'detail' field of a request.
		/*!
			@param array $data The repeat purchase data.
			@retval string A string to use as the 'detail' field of a request
		*/
		private function _getRequestDetailFromRepeatPurchaseData($data)
		{
			return sprintf('%sMin: %sMax: %s', 
				$data['ConsumableRepeatPurchase']['description'] . PHP_EOL,
				$data['ConsumableRepeatPurchase']['min'] . PHP_EOL,
				$data['ConsumableRepeatPurchase']['max']
			);
		}

		//! Given the id of a repeat purchase, get the id of the supplier most recently used to fulfil the request
		/*!
			@oaram int $id The id of the repeat purchase.
			@retval mixed Either the id of a supplier, or null of none found.
		*/
		private function _getLsatSupplierForRepeatPurchase($id)
		{			
			// Get the most recent fulfilled request for the repeat purchase
			$request = $this->find('first',
				array(
					'conditions' => array( 
						'ConsumableRequest.repeat_purchase_id' => $id,
						'ConsumableRequest.request_status_id' => ConsumableRequestStatus::FULFILLED,
					),
					'order' => array( 'ConsumableRequest.timestamp' => 'desc' ),
				)
			);
			if(!$request)
			{
				// No results found
				return null;
			}

			return $request['ConsumableRequest']['supplier_id'];
		}
	}
?>