<?php

	class StatusFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Status';

		public $records = array(
			array('status_id' => 1, 'title' => 'Prospective Member', 'description' => 'Interested in the hackspace, we have their e-mail. May be receiving the newsletter'),
			array('status_id' => 2, 'title' => 'Pre-Member (stage 1)', 'description' => 'Member has HMS login details, waiting for them to enter contact details'),
			array('status_id' => 3, 'title' => 'Pre-Member (stage 2)', 'description' => 'Waiting for member-admin to approve contact details'),
			array('status_id' => 4, 'title' => 'Pre-Member (stage 3)', 'description' => 'Waiting for standing order'),
			array('status_id' => 5, 'title' => 'Current Member', 'description' => 'Active member'),
			array('status_id' => 6, 'title' => 'Ex Member', 'description' => 'Former member, details only kept for a while'),
		);
	}

?>