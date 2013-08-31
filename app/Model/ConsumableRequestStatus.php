<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable request status data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableRequestStatus extends AppModel 
	{
		const PENDING = 1;
		const APPROVED = 2;
		const REJECTED = 3;
		const FULFILLED = 4;

		public $useTable = 'consumable_request_statuses';	//!< Specify the table to use.
		public $primaryKey = 'request_status_id';			//!< Specify the promary key to use.
	}
?>