<?php
	class Member extends AppModel {

		public $primaryKey = 'member_id';

		public $belongsTo =  array(
			"Status" => array(
					"className" => "Status",
					"foreignKey" => "member_status",
					"type" => "inner"
			),
			"Account" => array(
					"className" => "Account",
					"foreignKey" => "account_id",
					"type" => "inner"
			),
		);

		public $hasOne = array(
	        'Pin' => array(
	            'className'    => 'Pin',
	            'dependent'    => true
	        ),
	        'MemberAuth' => array(
	            'className'    => 'MemberAuth',
	            'dependent'    => true
	        ),
	    );

		public $hasAndBelongsToMany = array(
	        'Group' =>
	            array(
	                'className'              => 'Group',
	                'joinTable'              => 'member_group',
	                'foreignKey'             => 'member_id',
	                'associationForeignKey'  => 'grp_id',
	                'unique'                 => true,
	                'conditions'             => '',
	                'fields'                 => '',
	                'order'                  => '',
	                'limit'                  => '',
	                'offset'                 => '',
	                'finderQuery'            => '',
	                'deleteQuery'            => '',
	                'insertQuery'            => ''
	            ),
	    );

		public $validate = array(
	        'name' => array(
	            'rule' => 'notEmpty'
	        ),
	        'email' => array(
	        	'email'
	        ),
	        'handle' => array(
	            'rule' => 'notEmpty'
	        )
	    );


		public function beforeSave($options = array()) {
			# Have to do a few things before we save
			$memberWillBeCurrentMember = $this->data['Member']['member_status'] == 2;

			if( isset( $this->data['Member'] ) &&
				isset( $this->data['Member']['member_number'] ) === false &&
				$memberWillBeCurrentMember)
			{
				# We're setting this member to be a 'current member' for the first time, need to modify some things

				# Set the member number and join date
				# Member number is totally fucked up with hard-coded entries and missing entries, so we need to find what the highest number is
				$highestMemberNumber = $this->find( 'first', array( 'conditions' => array( 'Member.member_number !=' => null),  'order' => 'Member.member_number DESC', 'fields' => 'Member.member_number' ) );
				$this->data['Member']['member_number'] = $highestMemberNumber['Member']['member_number'] + 1;
				$this->data['Member']['join_date'] = date( 'Y-m-d' );
			}

			# Must never ever ever alter the balance
			unset( $this->data['Member']['balance'] );

			return true;
		}

		public function clearGroupsIfMembershipRevoked($id, $newData) {
			# If membership is being revoked, clear all groups
			if($newData['Member']['member_status'] == 3)
			{
				$this->MemberGroup->deleteAll(array( 'MemberGroup.member_id' => $id ));
			}
		}

		public function addToCurrentMemberGroupIfStatusIsCurrentMember($id, $newData) {
			# If membership is current_member, add to the current member group
			if($newData['Member']['member_status'] == 2)
			{
				$this->MemberGroup->deleteAll(array( 'MemberGroup.member_id' => $id, 'MemberGroup.grp_id' => 2 ));

				# Group 2 is for current members
				$currentGroups = Hash::extract($newData, 'Group.Group.{n}');
				print_r($currentGroups);
				if( in_array(2, $currentGroups) == false )
				{
					array_push($currentGroups, array( 'grp_id' => 2, 'member_id' => $id ));
				}

				$newData['Group']['Group'] = $currentGroups;

				$this->save($newData);
			}
		}

		# Returns true if the member is in the group
		public function memberInGroup($memberId, $groupId)
		{
			return in_array($groupId, Hash::extract( $this->find('first', array( 'conditions' => array( 'Member.member_id' => $memberId ) ) ), 'Group.{n}.grp_id' ));
		}
	}
?>