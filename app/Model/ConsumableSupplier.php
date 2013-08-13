<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all consumable supplier data
	 *
	 *
	 * @package       app.Model
	 */
	class ConsumableSupplier extends AppModel 
	{
		public $useTable = 'consumable_suppliers';	//!< Specify the table to use.
		public $primaryKey = 'supplier_id';			//!< Specify the promary key to use.
	}
?>