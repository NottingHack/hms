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
		public $primaryKey = 'purchase_id';					//!< Specify the promary key to use.

		//! We have a ConsumableArea
		public $hasOne = array(
			'ConsumableArea',
		);
	}
?>