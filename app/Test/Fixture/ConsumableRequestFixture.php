<?php

	class ConsumableRequestFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRequest';

		public $records = array(
			array( 'request_id' => 1, 'title' => 'a', 'detail' => 'a', 'url' => 'a', 'supplier_id' => null, 'area_id' => null, 	'repeat_purchase_id' => null ),
			array( 'request_id' => 2, 'title' => 'b', 'detail' => 'b', 'url' => 'b', 'supplier_id' => 1, 	'area_id' => null, 	'repeat_purchase_id' => null ),
			array( 'request_id' => 3, 'title' => 'c', 'detail' => 'c', 'url' => 'c', 'supplier_id' => 1, 	'area_id' => 1, 	'repeat_purchase_id' => null ),
			array( 'request_id' => 4, 'title' => 'd', 'detail' => 'd', 'url' => 'd', 'supplier_id' => 1, 	'area_id' => 1, 	'repeat_purchase_id' => 1 ),
			array( 'request_id' => 5, 'title' => 'e', 'detail' => 'e', 'url' => 'e', 'supplier_id' => 1, 	'area_id' => 1, 	'repeat_purchase_id' => 1 ),
			array( 'request_id' => 6, 'title' => 'f', 'detail' => 'f', 'url' => 'f', 'supplier_id' => 1, 	'area_id' => 1, 	'repeat_purchase_id' => 1 ),
			array( 'request_id' => 7, 'title' => 'g', 'detail' => 'g', 'url' => 'g', 'supplier_id' => 1, 	'area_id' => 1, 	'repeat_purchase_id' => 1 ),
		);
	}

?>