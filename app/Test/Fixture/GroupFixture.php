<?php

	class GroupFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Group';

		public $records = array(
			array('grp_id' => 1, 'grp_description' => 'Full Access'),
			array('grp_id' => 2, 'grp_description' => 'Current Members'),
			array('grp_id' => 3, 'grp_description' => 'Snackspace Admin'),
			array('grp_id' => 4, 'grp_description' => 'Gatekeeper Admin'),
		);
	}

?>