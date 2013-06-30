<?php

	class GroupsMemberFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $table = 'member_group';
		
		// This array looks pointless, but without it the create function below isn't called.
		public $fields = array(
          'member_id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
          'grp_id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
      	);

		public $records = array(
			array('member_id' => 1, 'grp_id' => 1),

			array('member_id' => 1, 'grp_id' => 2),
			array('member_id' => 2, 'grp_id' => 2),
			array('member_id' => 3, 'grp_id' => 2),
			array('member_id' => 4, 'grp_id' => 2),
			array('member_id' => 5, 'grp_id' => 2),

			array('member_id' => 2, 'grp_id' => 3),

			array('member_id' => 4, 'grp_id' => 4),

			array('member_id' => 5, 'grp_id' => 5),
		);

		public function create($db)
		{
			// Unfortunately due to the way our tables are set up
			// and the way CakePHP works, if you try to make CakePHP auto-generate this table
			// it doesn't work.
			// Thankfully we can override the creation here

			$sqlStatement = "CREATE TABLE IF NOT EXISTS `member_group` ( `member_id` int(11) NOT NULL, `grp_id` int(11) NOT NULL, PRIMARY KEY (`member_id`,`grp_id`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			return $db->execute($sqlStatement);
		}
	}

?>