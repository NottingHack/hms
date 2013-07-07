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
	    const MEMBERSHIP_ADMIN = 5; //!< The id of the membership admin group.
	    const MEMBERSHIP_TEAM = 6; 	//!< The id of the membership team group.
		
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

	    //! Get the Group description for a given id.
	    /*!
	    	@param int $groupId The primary key of the Group to get the description of.
	    	@retval mixed The description of the Group, or false if it can not be found.
	    */
	    public function getDescription($groupId)
	    {
	    	return $this->find('first', array('fields' => array('Group.grp_description'), 'conditions' => array('Group.grp_id' => $groupId)));
	    }

	    //! Get a summary of the group records for all groups.
		/*!
			@retval array A summary of the data of all groups.
			@sa Group::_getGroupSummary()
		*/
		public function getGroupSummaryAll()
		{
			return $this->_getGroupSummary();
		}

		//! Get a list of groups
		/*!
			@retval array A list of groups.
		*/
		public function getGroupList()
		{
			return $this->find('list', array('fields' => array('Group.grp_id', 'Group.grp_description')));
		}

		//! Get a summary of the group records for all groups that match the conditions.
		/*!
			@retval array A summary (id, name, description and member count) of the data of all groups that match the conditions.
		*/
		private function _getGroupSummary($conditions = array())
		{
			$info = $this->find( 'all', array('conditions' => $conditions) );

			return $this->_formatGroupInfo($info);
		}

		//! Format group information into a nicer arrangement.
		/*!
			@param $info The info to format, usually retrieved from Group::_getGroupSummary.
			@retval array An array of group information, formatted so that nothing needs to know database rows.
			@sa Group::_getGroupSummary
		*/
		private function _formatGroupInfo($info)
		{
			/*
	    	    Data should be presented to the view in an array like so:
	    			[n] => 
	    				[id] => group id
	    				[description] => group description
	    				[count] => number of members with this group
	    	*/

			$formattedInfo = array();
	    	foreach ($info as $group) 
	    	{
	    		$id = Hash::get($group, 'Group.grp_id');
	    		$description = Hash::get($group, 'Group.grp_description');
	    		$count = count( Hash::extract($group, 'Member') );

	    		array_push($formattedInfo,
	    			array(
	    				'id' => $id,
	    				'description' => $description,
	    				'count' => $count,
	    			)
	    		);
	    	}

	    	return $formattedInfo;
		}
	}
?>