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
			Has a ConsumableRequestStatus
		*/
		public $hasOne = array(
			'ConsumableRequestStatus',
			'ConsumableSupplier',
			'ConsumableArea',
			'ConsumableRepeatPurchase',
			'ConsumableRequestStatus',
		);

		/*!
			We have many ConsumableRequestComments
		*/
		public $hasMany = array(
			'ConsumableRequestComment',
		);
	}
?>