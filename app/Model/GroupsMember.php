<?php
	
	App::uses('AppModel', 'Model');

	/**
	 * Model for the table that defines the HABTM relationship between Group and Member
	 *
	 *
	 * @package       app.Model
	 */
	class GroupsMember extends AppModel 
	{
		public $useTable = 'member_group';	//!< Specify the table, as we don't use the default.

		//! We belong to both Member and Group.
		public $belongsTo = array(
			'Member' => array(
				'className' => 'Member',
				'foreignKey' => 'member_id',
			),
			'Group' => array(
				'className' => 'Group',
				'foreignKey' => 'grp_id',
			),
		);

		//! Test to see if a Member is in a Group.
		/*!
			@param int $memberId The primary key of the Member record.
			@param int $groupId The primary key of the Group record.
			@retval bool True if the Member is in the Group, false otherwise.
		*/
		public function is_member_in_group($memberId, $groupId)
		{
			$numEntries = $this->find('count', 
				array('conditions' => 
					array(
						'member_id' => $member_id, 
						'grp_id' => $groupId
					)
				)
			);

			return $numEntries > 0;
		}
	}
?>