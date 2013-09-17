<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable request status update data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableRequestStatusUpdate extends AppModel 
	{
		public $useTable = 'consumable_request_status_updates';		//!< Specify the table to use.
		public $primaryKey = 'request_status_update_id';			//!< Specify the promary key to use.

		public $belongsTo = array(
			'ConsumableRequestStatus' => array(
				'className' => 'ConsumableRequestStatus',
            	'foreignKey' => 'request_status_id'
			),
			'Member' => array(
				'className' => 'Member',
				'foreignKey' => 'member_id',
				'fields' => array( 'member_id', 'username' ),
			),
		);

		//! Validation rules.
	    public $validate = array(
	        'request_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Must have a valid request',
	        		'required' => true,
	        		'allowEmpty' => false,
	        	),
	        ),
	        'request_status_id' => array(
	            'number' => array(
	            	'rule' => array('naturalNumber', false),
	        		'message' => 'Request must have a valid status',
	            	'required' => true,
	            	'allowEmpty' => false,
	            )
	        ),
	        'member_id' => array(
	        	'number' => array(
	        		'rule' => array('naturalNumber', false),
	        		'message' => 'Member must be valid',
	        		'required' => false,
	        		'allowEmpty' => false,
	        	),
	        ),
	    );

	    //! Add a new status update
		/*
			@param int $requestId The id of the request the update is for.
			@param int $statusId The id of the new status of the request.
			@param mixed $memberId The id of the member making the comment, may be null.
			@retval bool True if comment was added, false otherwise.
		*/
		public function add($requestId, $statusId, $memberId)
		{
			$fields = array(
				'request_id' => $requestId,
				'request_status_id' => $statusId,
			);

			if($memberId != null)
			{
				$fields['member_id'] = $memberId;
			}

			$saveData = array(
				'ConsumableRequestStatusUpdate' => $fields
			);
			
			$this->create($saveData);
			if(!$this->validates())
			{
				throw new InvalidArgumentException('Information in $data did not correspond with validation rules');
			}

			return (bool)$this->save($saveData);
		}
	}
?>