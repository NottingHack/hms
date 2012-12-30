<?php

	class GroupsMember extends AppModel 
	{
		public $useTable = 'member_group';

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
						'group_id' => $groupId
					)
				)
			);

			return $numEntries > 0;
		}
	}
?>