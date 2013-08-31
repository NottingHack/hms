<?php

	class ConsumableRequestFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRequest';

		public $records = array(
			array( 'request_id' => 1, 'title' => 'a', 'detail' => 'a', 'url' => 'a', 'request_status_id' => 1, 'supplier_id' => null, 'area_id' => null, 'repeat_purchase_id' => null ),
			array( 'request_id' => 2, 'title' => 'b', 'detail' => 'b', 'url' => 'b', 'request_status_id' => 1, 'supplier_id' => 1, 'area_id' => null, 'repeat_purchase_id' => null ),
			array( 'request_id' => 3, 'title' => 'c', 'detail' => 'c', 'url' => 'c', 'request_status_id' => 1, 'supplier_id' => 1, 'area_id' => 1, 'repeat_purchase_id' => null ),
			array( 'request_id' => 4, 'title' => 'd', 'detail' => 'd', 'url' => 'd', 'request_status_id' => 1, 'supplier_id' => 1, 'area_id' => 1, 'repeat_purchase_id' => 1 ),
			array( 'request_id' => 5, 'title' => 'e', 'detail' => 'e', 'url' => 'e', 'request_status_id' => 2, 'supplier_id' => 1, 'area_id' => 1, 'repeat_purchase_id' => 1 ),
			array( 'request_id' => 6, 'title' => 'f', 'detail' => 'f', 'url' => 'f', 'request_status_id' => 3, 'supplier_id' => 1, 'area_id' => 1, 'repeat_purchase_id' => 1 ),
			array( 'request_id' => 7, 'title' => 'g', 'detail' => 'g', 'url' => 'g', 'request_status_id' => 4, 'supplier_id' => 1, 'area_id' => 1, 'repeat_purchase_id' => 1 ),
		);
	}

?>