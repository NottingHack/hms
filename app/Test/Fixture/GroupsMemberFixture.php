<?php

	class GroupsMemberFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $table = 'member_group';
		//public $import = 'GroupsMember';
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
			return true;
		}

		public function drop($db)
		{
			$this->truncate($db);
			return true;
		}
	}

?>