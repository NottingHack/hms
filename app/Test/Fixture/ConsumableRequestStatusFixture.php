<?php

	class ConsumableRequestStatusFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRequestStatus';

		public $records = array(
			array( 'request_status_id' => 1, 'name' => 'Pending' ),
			array( 'request_status_id' => 2, 'name' => 'Approved' ),
			array( 'request_status_id' => 3, 'name' => 'Rejected' ),
			array( 'request_status_id' => 4, 'name' => 'Fulfilled' ),
		);
	}

?>