<?php

	class ConsumableRepeatPurchaseFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRepeatPurchase';

		public $records = array(
			array( 'repeat_purchase_id' => 1, 'name' => 'a', 'description' => 'a', 'min' => '1', 'max' => '10', 'area_id' => 1 ),
			array( 'repeat_purchase_id' => 2, 'name' => 'b', 'description' => 'b', 'min' => '1', 'max' => '10', 'area_id' => 1 ),
			array( 'repeat_purchase_id' => 3, 'name' => 'c', 'description' => 'c', 'min' => '1', 'max' => '10', 'area_id' => 2 ),
		);
	}

?>