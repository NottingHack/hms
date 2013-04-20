<?php

	class StatusUpdateFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'StatusUpdate';

		public function init()
		{
			$this->records = array(
				array('id' => 1, 'member_id' => 1, 'admin_id' => 3, 'old_status' => 0, 'new_status' => 1, 'timestamp' => date('Y-m-d H:i:s')),
				array('id' => 2, 'member_id' => 4, 'admin_id' => 5, 'old_status' => 4, 'new_status' => 5, 'timestamp' => '2012-12-17 19:19:59'),
			);

			parent::init();
		}
	}

?>