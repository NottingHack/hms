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
			array('hms_email_id' => 5, 'member_id' => 4, 'subject' => 'Test email 3', 'timestamp' => '2013-01-23 09:23:42' ),
			array('hms_email_id' => 6, 'member_id' => 4, 'subject' => 'Test email 5', 'timestamp' => '2013-03-16 15:15:23' ),
			array('hms_email_id' => 7, 'member_id' => 4, 'subject' => 'Test email 6', 'timestamp' => '2013-02-19 17:47:26' ),
			array('hms_email_id' => 8, 'member_id' => 4, 'subject' => 'Test email 7', 'timestamp' => '2013-04-13 22:51:09' ),
		);
	}

?>