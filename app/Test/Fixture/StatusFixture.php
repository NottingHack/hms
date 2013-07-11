<?php

	class StatusFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Status';

		public $records = array(
			array('status_id' => 1, 'title' => 'Prospective Member'),
			array('status_id' => 2, 'title' => 'Waiting for contact details'),
			array('status_id' => 3, 'title' => 'Waiting for Membership Admin to approve contact details'),
			array('status_id' => 4, 'title' => 'Waiting for standing order payment'),
			array('status_id' => 5, 'title' => 'Current Member'),
			array('status_id' => 6, 'title' => 'Ex Member'),
		);
	}

?>