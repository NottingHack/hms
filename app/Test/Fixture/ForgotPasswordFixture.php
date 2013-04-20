<?php

	class ForgotPasswordFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ForgotPassword';

		public function init()
		{
			$this->records = array(
				array('member_id' => 1, 'request_guid' => '50b0ec45-8984-48b8-ac8a-5db90a000005', 'timestamp' => '2012-11-24 15:48:21', 'expired' => 1), // Old request that's been expired
				array('member_id' => 2, 'request_guid' => '50b104e4-33f8-4821-b756-5e100a000005', 'timestamp' => date('Y-m-d H:i:s'), 'expired' => 0),	// Current request
				array('member_id' => 3, 'request_guid' => '50be19c8-0968-43ba-be1b-0990bcda665d', 'timestamp' => date('Y-m-d H:i:s', time() - (3 * 60 * 60)), 'expired' => 0),	// Request that's expired due to time (3 hours old)
			);

			parent::init();
		}
	}

?>