<?php

	class EmailRecordFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'EmailRecord';

		public $records = array(
			array('hms_email_id' => 1, 'member_id' => 1, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:19:59' ),
			array('hms_email_id' => 2, 'member_id' => 2, 'subject' => 'Test email 1', 'timestamp' => '2012-12-17 19:20:00' ),
			array('hms_email_id' => 3, 'member_id' => 3, 'subject' => 'Test email 2', 'timestamp' => '2013-03-23 05:42:21' ),
			array('hms_email_id' => 4, 'member_id' => 4, 'subject' => 'Test email 2', 'timestamp' => '2013-06-05 13:51:04' ),
		);
	}

?>