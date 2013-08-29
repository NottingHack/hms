<?php

	class ConsumableSupplierFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableSupplier';

		public $records = array(
			array( 'supplier_id' => 1, 'name' => 'a', 'description' => 'a', 'address' => 'a', 'url' => 'a' ),
			array( 'supplier_id' => 2, 'name' => 'b', 'description' => 'b', 'address' => 'b', 'url' => 'b' ),
		);
	}

?>