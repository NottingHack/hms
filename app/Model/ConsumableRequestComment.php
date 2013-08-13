<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable request comment data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableRequestComment extends AppModel 
	{
		public $useTable = 'consumable_request_comments';	//!< Specify the table to use.
		public $primaryKey = 'comment_id';					//!< Specify the promary key to use.

		/*!
			We belong to a ConsumableRequest
		*/
		public $belongsTo = array(
			'ConsumableRequest',
		);
	}
?>