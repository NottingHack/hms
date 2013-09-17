<?php

	class ConsumableRequestStatusUpdateFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRequestStatusUpdate';

		public $records = array(
			array( 'request_status_update_id' => 1, 'request_id' => 1, 'request_status_id' => 1, 'member_id' => 1, 'timestamp' => '2013-08-31 09:00:00' ),
			array( 'request_status_update_id' => 2, 'request_id' => 1, 'request_status_id' => 2, 'member_id' => 2, 'timestamp' => '2013-08-31 10:00:00' ),
			array( 'request_status_update_id' => 3, 'request_id' => 1, 'request_status_id' => 3, 'member_id' => 2, 'timestamp' => '2013-08-31 11:00:00' ),
		);
	}

?>