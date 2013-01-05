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
		public function isMemberInGroup($memberId, $groupId)
		{
			$numEntries = $this->find('count', 
				array('conditions' => 
					array(
						'GroupsMember.member_id' => $memberId, 
						'GroupsMember.grp_id' => $groupId
					)
				)
			);

			return $numEntries > 0;
		}

		//! Get the id of any Group that a Member is in.
		/*!
			@param int $memberId The primary key of the Member record.
			@retval array An array of Group ids that the Member belongs to.
		*/
		public function getGroupIdsForMember($memberId)
		{
			return $this->find('list', array('fields' => array('GroupsMember.grp_id', 'GroupsMember.grp_id'), 'conditions' => array('GroupsMember.member_id' => $memberId)));
		}
	}
?>