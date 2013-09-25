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
			'ConsumableRequestStatusUpdate' => array(
				'className' => 'ConsumableRequestStatusUpdate',
            	'foreignKey' => 'request_id',
            	'order' => 'ConsumableRequestStatusUpdate.timestamp DESC',
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
	        'request_id' => array(
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

		//! Add a new request
		/*!
			@param array $data An array of data to create the request.
			@param mixed $memberId Either the id of the adding the request, or null if anon request.
			@retval bool True if record was created successfully, false otherwise.
		*/
		public function add($data, $memberId)
		{
			// If memberId is not null, it must be a positive integer
			if(!is_null($memberId))
			{
				$memberIdIsValid = is_integer($memberId) && $memberId > 0;
				if(!$memberIdIsValid)
				{
					throw new InvalidArgumentException('$memberId must be a greater-than-zero integer');
				}
			}
			
			$this->create($data);
			if(!$this->validates())
			{
				throw new InvalidArgumentException('Information in $data did not correspond with validation rules');
			}

			// Start a transaction, so we can roll all this back
			// if we can't create the status update (for some reason)
			$dataSource = $this->getDataSource();
			$dataSource->begin();

			if($this->save($data))
			{
				$requestId = $this->id;
				// The status of a request being added must be 'Pending'
				if($this->ConsumableRequestStatusUpdate->add($requestId, ConsumableRequestStatus::PENDING, $memberId))
				{					
					$dataSource->commit();
					return true;
				}
			}

			$dataSource->rollback();
			return false;
		}

		//! Add a new request from a repeat purchase
		/*!
			@param int $repeatPurchaseId The id of the repeat purchase to use to create the request.
			@param mixed $memberId Either the id of the adding the request, or null if annon request.
			@retval bool True if records was created successfully, false otherwise.
		*/
		public function addFromRepeatPurchase($repeatPurchaseId, $memberId)
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
					'supplier_id' => $this->_getLastSupplierForRepeatPurchase($repeatPurchaseId),
					'area_id' => $repeatPurchaseRecord['ConsumableRepeatPurchase']['area_id'],
					'repeat_purchase_id' => $repeatPurchaseId,
				),
			);

			return $this->add($addData, $memberId);
		}

		//! Get the request information for a record.
		/*!
			@param int $id The if of the request to get the information from.
			@retval array An array of request data.
		*/
		public function get($id)
		{
			if(!is_numeric($id) || 
				$id <= 0)
			{
				throw new InvalidArgumentException('$id must be numeric and greater than zero');
			}

			$record = $this->find('first',
				array(
					'conditions' => array(
						'ConsumableRequest.request_id' => $id
					),
					'recursive' => 2,
				)
			);
			if(!is_array($record) || count($record) == 0)
			{
				return array();
			}

			return $this->_formatRecord($record);
		}

		//! Get the request information for all records.
		/*!
			@retval array An array of request data
		*/
		public function getAll()
		{
			$formattedRecords = array();
			foreach ($this->find('all', array('recursive' => 2)) as $index => $record) 
			{
				array_push($formattedRecords, $this->_formatRecord($record));
			}
			return $formattedRecords;
		}

		//! Get all the request infomation for requests that are currently set to a certain status
		/*!
			@param int $status The id of the status to look for.
			@retval array An array or request data.
		*/
		public function getAllWithStatus($status)
		{
			if(!is_numeric($status) || 
				$status <= 0)
			{
				throw new InvalidArgumentException('$id must be numeric and greater than zero');
			}

			$matchingRecords = array();
			foreach ($this->getAll() as $record) 
			{
				$recordStatus = Hash::get($record, 'currentStatus.request_status_id');
				if($recordStatus == $status)
				{
					array_push($matchingRecords, $record);
				}
			}
			
			return $matchingRecords;
		}

		//! Get an overview of consumable request data
		/*
			@retval attay An overview of how many requests exist in the system and what status they are currently at.
		*/
		public function getOverviewData()
		{
			// First grab all the status information and map that to a friendlier array which includes
			// a count element.
			$statuses = $this->ConsumableRequestStatusUpdate->ConsumableRequestStatus->find('all');
			$statusAndCounts = Hash::map($statuses, '{n}.ConsumableRequestStatus', function ($record)
			{
				return array(
						'id' => $record['request_status_id'],
						'name' => $record['name'],
						'count' => 0,
				);
			});

			// Now grab all the records and map them to the status that they're currently at
			$allRequests = $this->getAll();
			Hash::map($allRequests, '{n}.currentStatus.request_status_id', function($status) use(&$statusAndCounts)
			{
				for($i = 0; $i < count($statusAndCounts); $i++)
				{
					if($statusAndCounts[$i]['id'] == $status)
					{
						$statusAndCounts[$i]['count']++;
					}
				}
			});

			return $statusAndCounts;			
		}

		//! Get the request information for all records that involve a certain member.
		/*!
			@param int $id The id of member to look for.
			@retval array An array of request data for any requests opened by or commented on by the member.
		*/
		public function getRequestsInvolvingMember($memberId)
		{
			if(!is_numeric($memberId) || 
				$memberId <= 0)
			{
				throw new InvalidArgumentException('$memberId must be numeric and greater than zero');
			}

			$requestsOpenedByMember = array();
			$requestsCommentedOnByMember = array();
			foreach ($this->getAll() as $record) 
			{
				$openedByMember = Hash::check($record, "firstStatus[member_id=$memberId]");
				if($openedByMember)
				{
					array_push($requestsOpenedByMember, $record);
				}
				else
				{
					$commentedOnByMember = Hash::check($record, "comments.{n}[member_id=$memberId]");
					if($commentedOnByMember)
					{
						array_push($requestsCommentedOnByMember, $record);
					}
				}
			}
			
			return array(
				'openedBy' => $requestsOpenedByMember,
				'commentedOn' => $requestsCommentedOnByMember,
			);
		}

		//! Format a record for use outside of this class
		/*!
			@param array $record The data to be formatted.
			@retval array The formatted data.
		*/
		private function _formatRecord($record)
		{
			$formattedData = $record['ConsumableRequest'];
			$formattedData['supplier'] = $record['ConsumableSupplier'];
			$formattedData['area'] = $record['ConsumableArea'];
			$formattedData['repeatPurchase'] = $record['ConsumableRepeatPurchase'];

			$formattedComments = array();
			foreach ($record['ConsumableRequestComment'] as $comment)
			{
				$comment['member_username'] = Hash::get($comment, 'Member.username');
				unset($comment['Member']);
				array_push($formattedComments, $comment);
			}
			$formattedData['comments'] = $formattedComments;
			
			$formattedStatuses = array();
			$firstStatus = array();
			$currentStatus = array();

			$statusUpdates = $record['ConsumableRequestStatusUpdate'];
			$numStatuses = count($statusUpdates);
			for($i = 0; $i < $numStatuses; $i++)
			{
				$rawStatus = $statusUpdates[$i];
				$formattedStatus = $rawStatus;
				$formattedStatus['request_status_name'] = Hash::get($rawStatus, 'ConsumableRequestStatus.name');
				$formattedStatus['member_username'] = Hash::get($rawStatus, 'Member.username');
				unset($formattedStatus['ConsumableRequestStatus']);
				unset($formattedStatus['Member']);

				array_push($formattedStatuses, $formattedStatus);
				if($i == 0)
				{
					$currentStatus = $formattedStatus;
				}

				if($i == $numStatuses - 1)
				{
					$firstStatus = $formattedStatus;
				}
			}
			$formattedData['statuses'] = $formattedStatuses;
			$formattedData['firstStatus'] = $firstStatus;
			$formattedData['currentStatus'] = $currentStatus;

			return $formattedData;
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

		//! Given the id of a repeat purchase, get the id of the request most recently used to fulfil the request
		/*!
			@oaram int $id The id of the repeat purchase.
			@retval mixed Either the id of a request, or null of none found.
		*/
		private function _getLastSupplierForRepeatPurchase($id)
		{			
			// Get the most recent fulfilled request for the repeat purchase
			// Have to use this rather beastly join to override the default joins
			// made with the hasOne/belongsTo associations
			$records = $this->find('first',
				array(
					'fields' => array(
						'ConsumableRequest.*',
						'CurrentStatus.*',
					),
					'joins' => array(
						array(
							'table' => 'consumable_request_status_updates',
							'alias' => 'CurrentStatus',
							'type' => 'INNER',
							'conditions'=> array(
								'CurrentStatus.request_id = ConsumableRequest.request_id',
							),
						),
					),
					'conditions' => array( 
						'ConsumableRequest.repeat_purchase_id' => $id,
						'CurrentStatus.request_status_id' => ConsumableRequestStatus::FULFILLED,
					),
					'order' => 'CurrentStatus.timestamp DESC',
				)
			);

			if(!$records)
			{
				// No results found
				return null;
			}

			return $records['ConsumableRequest']['supplier_id'];
		}
	}
?>