<?php
	class Group extends AppModel {
		
		public $useTable = "grp";

		public $primaryKey = 'grp_id';

		public $hasAndBelongsToMany = array(
	        'Permission' =>
	            array(
	                'className' => 'Permission',
	                'joinTable' => 'group_permissions',
	                'foreignKey' => 'grp_id',
	                'associationForeignKey' => 'permission_code',
	                'unique' => true,
	            ),
	        'Member' =>
	            array(
	                'className' => 'Member',
	                'joinTable' => 'member_group',
	                'foreignKey' => 'grp_id',
	                'associationForeignKey' => 'member_id',
	                'unique' => true,
	                'with' => 'GroupsMember',
	            )
	    );

	    public $validate = array(
	        'grp_description' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

	    const FULL_ACCESS = 1; 		//!< The id of the full access group.
	    const CURRENT_MEMBERS = 2; 	//!< The id of the current members group.
	    const GATEKEEPER_ADMIN = 3; //!< The id of the gatekeeper admin group.
	    const SNACKSPACE_ADMIN = 4; //!< The id of the snackspace admin group.
	}
?>