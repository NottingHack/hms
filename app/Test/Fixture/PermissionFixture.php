<?php

	class PermissionFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Permission';

		public $records = array(
			array('permission_code' => 'ADD_GROUP', 'permission_desc' => 'Add group'),
			array('permission_code' => 'ADD_GRP_MEMBER', 'permission_desc' => 'Add member to group'),
			array('permission_code' => 'ADD_MEMBER', 'permission_desc' => 'Add member'),
			array('permission_code' => 'ADD_UPD_PRODUCT', 'permission_desc' => 'Add / update product'),
			array('permission_code' => 'AMEND_PINS', 'permission_desc' => 'Add / Cancel PINs'),
			array('permission_code' => 'CHG_GRP_PERM', 'permission_desc' => 'Change/toggle state of group permissions'),
			array('permission_code' => 'DEL_GROUP', 'permission_desc' => 'Delete group'),
			array('permission_code' => 'REC_TRAN', 'permission_desc' => 'Record transaction (against any member)'),
			array('permission_code' => 'REC_TRAN_OWN', 'permission_desc' => 'Record transaction (against self)'),
			array('permission_code' => 'REM_GRP_MEMBER', 'permission_desc' => 'Remove member from group'),
			array('permission_code' => 'SET_CREDIT_LIMIT', 'permission_desc' => 'Set member credit limit'),
			array('permission_code' => 'SET_PASSWORD', 'permission_desc' => 'Set any members password'),
			array('permission_code' => 'UPD_VEND_CONFIG', 'permission_desc' => 'Update vending machine config'),
			array('permission_code' => 'VIEW_ACCESS_MEM', 'permission_desc' => 'View Access > Members'),
			array('permission_code' => 'VIEW_BALANCES', 'permission_desc' => 'View member balances / credit limit'),
			array('permission_code' => 'VIEW_GROUPS', 'permission_desc' => 'View list of access groups'),
			array('permission_code' => 'VIEW_GRP_MEMBERS', 'permission_desc' => 'View group members'),
			array('permission_code' => 'VIEW_GRP_PERMIS', 'permission_desc' => 'View group permissions'),
			array('permission_code' => 'VIEW_MEMBERS', 'permission_desc' => 'View members list (add member to group listbox - handle+id only)'),
			array('permission_code' => 'VIEW_MEMBER_LIST', 'permission_desc' => 'View full members list'),
			array('permission_code' => 'VIEW_MEMBER_PINS', 'permission_desc' => 'View entry PINs'),
			array('permission_code' => 'VIEW_MEMBER_RFID', 'permission_desc' => 'View registered RFID card details'),
			array('permission_code' => 'VIEW_OWN_TRANS', 'permission_desc' => 'View own transactions'),
			array('permission_code' => 'VIEW_PRD_DETAIL', 'permission_desc' => 'View product details'),
			array('permission_code' => 'VIEW_PRODUCTS', 'permission_desc' => 'View products'),
			array('permission_code' => 'VIEW_SALES', 'permission_desc' => 'View sales list of a product (inc. handle of purchaser)'),
			array('permission_code' => 'VIEW_TRANS', 'permission_desc' => 'View member transactions'),
			array('permission_code' => 'VIEW_VEND_CONFIG', 'permission_desc' => 'View vending machine setup (product in each location)'),
			array('permission_code' => 'VIEW_VEND_LOG', 'permission_desc' => 'View vending machine log'),
			array('permission_code' => 'WEB_LOGON', 'permission_desc' => 'Allow logon to nh-web'),
		);
	}

?>