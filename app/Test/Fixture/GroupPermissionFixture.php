<?php

	class GroupPermissionFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $table = 'group_permissions';

		// This array looks pointless, but without it the create function below isn't called.
		public $fields = array(
			'grp_id' => array('type' => 'integer', 'null' => false, 'key' => 'primary'),
          	'permission_code' => array('type' => 'varchar', 'length' => 16, 'null' => false, 'key' => 'primary'),
		);

		public $records = array(
			array('grp_id' => 1, 'permission_code' => 'ADD_GROUP'),
			array('grp_id' => 1, 'permission_code' => 'ADD_GRP_MEMBER'),
			array('grp_id' => 1, 'permission_code' => 'ADD_MEMBER'),
			array('grp_id' => 1, 'permission_code' => 'ADD_UPD_PRODUCT'),
			array('grp_id' => 1, 'permission_code' => 'AMEND_PINS'),
			array('grp_id' => 1, 'permission_code' => 'CHG_GRP_PERM'),
			array('grp_id' => 1, 'permission_code' => 'DEL_GROUP'),
			array('grp_id' => 1, 'permission_code' => 'REC_TRAN'),
			array('grp_id' => 1, 'permission_code' => 'REC_TRAN_OWN'),
			array('grp_id' => 1, 'permission_code' => 'REM_GRP_MEMBER'),
			array('grp_id' => 1, 'permission_code' => 'SET_CREDIT_LIMIT'),
			array('grp_id' => 1, 'permission_code' => 'SET_PASSWORD'),
			array('grp_id' => 1, 'permission_code' => 'UPD_VEND_CONFIG'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_ACCESS_MEM'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_BALANCES'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_GROUPS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_GRP_MEMBERS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_GRP_PERMIS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_MEMBERS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_MEMBER_LIST'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_MEMBER_PINS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_MEMBER_RFID'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_OWN_TRANS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_PRD_DETAIL'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_PRODUCTS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_SALES'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_TRANS'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_VEND_CONFIG'),
			array('grp_id' => 1, 'permission_code' => 'VIEW_VEND_LOG'),
			array('grp_id' => 1, 'permission_code' => 'WEB_LOGON'),
			array('grp_id' => 2, 'permission_code' => 'REC_TRAN_OWN'),
			array('grp_id' => 2, 'permission_code' => 'VIEW_OWN_TRANS'),
			array('grp_id' => 2, 'permission_code' => 'VIEW_PRD_DETAIL'),
			array('grp_id' => 2, 'permission_code' => 'VIEW_PRODUCTS'),
			array('grp_id' => 2, 'permission_code' => 'VIEW_VEND_CONFIG'),
			array('grp_id' => 2, 'permission_code' => 'WEB_LOGON'),
			array('grp_id' => 3, 'permission_code' => 'ADD_UPD_PRODUCT'),
			array('grp_id' => 3, 'permission_code' => 'REC_TRAN'),
			array('grp_id' => 3, 'permission_code' => 'REC_TRAN_OWN'),
			array('grp_id' => 3, 'permission_code' => 'SET_CREDIT_LIMIT'),
			array('grp_id' => 3, 'permission_code' => 'UPD_VEND_CONFIG'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_BALANCES'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_OWN_TRANS'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_PRD_DETAIL'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_PRODUCTS'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_SALES'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_TRANS'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_VEND_CONFIG'),
			array('grp_id' => 3, 'permission_code' => 'VIEW_VEND_LOG'),
			array('grp_id' => 3, 'permission_code' => 'WEB_LOGON'),
			array('grp_id' => 4, 'permission_code' => 'ADD_MEMBER'),
			array('grp_id' => 4, 'permission_code' => 'AMEND_PINS'),
			array('grp_id' => 4, 'permission_code' => 'VIEW_ACCESS_MEM'),
			array('grp_id' => 4, 'permission_code' => 'VIEW_MEMBERS'),
			array('grp_id' => 4, 'permission_code' => 'VIEW_MEMBER_LIST'),
			array('grp_id' => 4, 'permission_code' => 'VIEW_MEMBER_PINS'),
			array('grp_id' => 4, 'permission_code' => 'VIEW_MEMBER_RFID'),
		);


		public function create($db)
		{
			// Unfortunately due to the way our tables are set up
			// and the way CakePHP works, if you try to make CakePHP auto-generate this table
			// it doesn't work.
			// Thankfully we can override the creation here

			$sqlStatement = "CREATE TABLE IF NOT EXISTS `group_permissions` ( `grp_id` int(11) NOT NULL, `permission_code` varchar(16) NOT NULL, PRIMARY KEY (`grp_id`,`permission_code`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
			return $db->execute($sqlStatement);
		}
	}

?>