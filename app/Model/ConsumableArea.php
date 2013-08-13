<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable area data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableArea extends AppModel 
	{
		public $useTable = 'consumable_areas';	//!< Specify the table to use.
		public $primaryKey = 'area_id';			//!< Specify the promary key to use.
	}
?>