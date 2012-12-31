<?php
	
	App::uses('AppModel', 'Model');

	/**
	 * Model for all group data
	 *
	 *
	 * @package       app.Model
	 */
	class Group extends AppModel
	{

		const FULL_ACCESS = 1; 		//!< The id of the full access group.
	    const CURRENT_MEMBERS = 2; 	//!< The id of the current members group.
	    const GATEKEEPER_ADMIN = 3; //!< The id of the gatekeeper admin group.
	    const SNACKSPACE_ADMIN = 4; //!< The id of the snackspace admin group.
	    const MEMBER_ADMIN = 5; //!< The id of the snackspace admin group.
		
		public $useTable = "grp";	//!< Specify the table to use, as we don't use the default.

		public $primaryKey = 'grp_id';	//!< Specify the primary key to use, as we don't use the default.

		//! We have and belong to many Permission and Member.
	    /*!
	    	Permission is set to be unique as it is impossible for the Group to have the same Permission twice.
	    	Member is set to be unique as it is impossible for the Group to contain the same Member twice.
	    	We also specify a model to use as the 'with' model so that we can add methods to it.
	    */
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

		//! Validation rules.
		/*!
			The group description must not be empty.
		*/
	    public $validate = array(
	        'grp_description' => array(
	            'rule' => 'notEmpty'
	        ),
	    );
	}
?>