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
		public $useTable = 'consumable_request_statuses';	//!< Specify the table to use.
		public $primaryKey = 'status_id';					//!< Specify the promary key to use.
	}
?>