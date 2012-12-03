<?php

	App::uses('AppModel', 'Model');

	class Member extends AppModel {

		const MIN_PASSWORD_LENGTH = 6;

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
					#"type" => "inner"
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
	        'password' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	        	'minLen' => array(
	        		'rule' => array('minLength', self::MIN_PASSWORD_LENGTH),
            		'message' => 'Password too short',
            	),
	        ),
	        'password_confirm' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	            'minLen' => array(
	        		'rule' => array('minLength', self::MIN_PASSWORD_LENGTH),
            		'message' => 'Password too short',
            	),
	        	'matchNewPassword' => array(
	            	'rule' => array( 'passwordConfirmMatchesPassword' ),
	            	'message' => 'Passwords don\'t match',
	            ),
	        ),
	        'username' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	        	'mustbeUnique' => array(
	            	'rule' => array( 'checkUniqueUsername' ),
	            	'message' => 'That username is already taken',
	            )
	        ),
	        'address_1' => array(
	            'rule' => 'notEmpty'
	        ),
	        'address_city' => array(
	            'rule' => 'notEmpty'
	        ),
	        'address_postcode' => array(
	            'rule' => 'notEmpty'
	        ),
	        'contact_number' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

	    public function passwordConfirmMatchesPassword($check)
		{
			return $this->data['Member']['password'] == $this->data['Member']['password_confirm'];
		}

		public function checkUniqueUsername($check)
		{
			$lowercaseUsername = strtolower($this->data['Member']['username']);
			$records = $this->find('all', array(  'fields' => array('Member.username'),
													'conditions' => array( 
														'Member.username LIKE' => $lowercaseUsername,
														'Member.member_id NOT' => $this->data['Member']['member_id'],
													) 
												)
			);

			foreach ($records as $record) {
				if(strtolower($record['Member']['username']) == $lowercaseUsername)
				{
					return false;
				}
			}
			return true;
		}

		public function beforeSave($options = array()) {

			if( isset($this->data['Member']['member_status']) )
			{
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
			}

			# Must never ever ever alter the balance
			unset( $this->data['Member']['balance'] );

			return true;
		}

		public function clearGroupsIfMembershipRevoked($id, $newData) {
			if( isset($newData['Member']['member_status']) )
			{
				# If membership is being revoked, clear all groups
				if($newData['Member']['member_status'] == 3)
				{
					$this->MemberGroup->deleteAll(array( 'MemberGroup.member_id' => $id ));
				}
			}
		}

		public function addToCurrentMemberGroupIfStatusIsCurrentMember($id, $newData) {

			if( isset($newData['Member']['member_status']) )
			{
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
		}

		# Returns true if the member is in the group
		public function memberInGroup($memberId, $groupId)
		{
			return in_array($groupId, Hash::extract( $this->find('first', array( 'conditions' => array( 'Member.member_id' => $memberId ) ) ), 'Group.{n}.grp_id' ));
		}

		public static function isInGroup($user, $groupId)
		{
			if(	isset($user) &&
				isset($user['Group']))
			{
				return in_array($groupId, Hash::extract($user['Group'], '{n}.grp_id'));
			}

			return false;
		}

		public static function isInGroupFullAccess($user)
		{
			return Member::isInGroup($user, 1);
		}

		public static function isInGroupMemberAdmin($user)
		{
			return Member::isInGroup($user, 5);
		}

		public static function isInGroupTourGuide($user)
		{
			return Member::isInGroup($user, 6);
		}
	}
?>