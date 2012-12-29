<?php

	App::uses('AppModel', 'Model');


	/**
	 * Model for all member data
	 *
	 *
	 * @package       app.Model
	 */
	class Member extends AppModel {

		const MIN_PASSWORD_LENGTH = 6; //!< The minimum length passwords must be.
		const MIN_USERNAME_LENGTH = 3; //!< The minimum length usernames must be.
		const MAX_USERNAME_LENGTH = 30; //!< The maximum length usernames can be.

		public $primaryKey = 'member_id'; //!< Specify the primary key, since we don't use the default.

		//! We belong to both the Status and Account models.
		/*! 
			Status should be joined on an inner join as it makes no sense to have no status.
			Account should be likewise, but isn't because we have to play nice with the existing data.
		*/
		public $belongsTo =  array(
			'Status' => array(
					'className' => 'Status',
					'foreignKey' => 'member_status',
					'type' => 'inner'
			),
			'Account' => array(
					'className' => 'Account',
					'foreignKey' => 'account_id',
			),
		);

		//! We have a Pin.
		/*!
			Pin is set to be dependant so it will be deleted when the Member is.
		*/
		public $hasOne = array(
	        'Pin' => array(
	            'className'    => 'Pin',
	            'dependent'    => true
	        ),
	    );

		//! We have many StatusUpdate.
		/*!
			We only (normally) care about the most recent Status Update.
		*/
	    public $hasMany = array(
	    	'StatusUpdate' => array(
	    		'order' => 'StatusUpdate.timestamp DESC',
	    		'limit'	=> '1',	
	    	)
	    );

	    //! We have and belong to many Group.
	    /*!
	    	Group is set to be unique as it is impossible for the Member to be in the same Group twice.
	    */
		public $hasAndBelongsToMany = array(
	        'Group' =>
	            array(
	                'className'              => 'Group',
	                'joinTable'              => 'member_group',
	                'foreignKey'             => 'member_id',
	                'associationForeignKey'  => 'grp_id',
	                'unique'                 => true,
	            ),
	    );

		//! Validation rules.
		/*!
			Name must not be empty.
			Email must be a valid email (and not empty).
			Password must not be empty and have a length equal or greater than the Member::MIN_PASSWORD_LENGTH.
			Password Confirm must not be empty, have a length equal or greater than the Member::MIN_PASSWORD_LENGTH and it's contents much match that of Password.
			Username must not be empty, be unique (in the database), only contain alpha-numeric characters, and be between Member::MIN_USERNAME_LENGTH and Member::MAX_USERNAME_LENGTH characters long.
			Address 1 must not be empty.
			Address City must not be empty.
			Address Postcode must not be empty.
			Contact Number must not be empty.
			No further validation is performed on the Address and Contact Number fields as a member admin has to check these during membership registration.
		*/
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
	            ),
	            'alphaNumeric' => array(
	                'rule'     => 'alphaNumeric',
	                'message'  => 'Aplha-numeric characters only'
	            ),
	            'between' => array(
	                'rule'    => array('between', self::MIN_USERNAME_LENGTH, self::MAX_USERNAME_LENGTH),
	                'message' => 'Between 3 to 30 characters'
	            ),

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

		//! Validation function to see if the user-supplied password and password confirmation match.
		/*!
			@param array $check The password to be validated.
			@retval bool True if the supplied password values match, otherwise false.
		*/
	    public function passwordConfirmMatchesPassword($check)
		{
			return $this->data['Member']['password'] === $check;
		}

		//! Validation function to see if the user-supplied username is already taken.
		/*!
			@param array $check The username to check.
			@retval bool True if the supplied username doesn't exist in the database, otherwise false.
		*/
		public function checkUniqueUsername($check)
		{
			$lowercaseUsername = strtolower($check);
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

		//! Validation function to see if the user-supplied email matches what's in the database.
		/*!
			@param array $check The email to check.
			@retval bool True if the supplied email value matches the database, otherwise false.
			@sa Member::addEmailMustMatch()
			@sa Member::removeEmailMustMatch()
		*/
		public function checkEmailMatch($check)
		{
			$ourEmail = $this->find('first', array('fields' => array('Member.email'), 'conditions' => array('Member.member_id' => $this->data['Member']['member_id'])));
			return strcasecmp($ourEmail['Member']['email'], $check) == 0;
		}

		//! Actions to perform before saving any data
		/*!
			@param array $options Any options that were passed to the Save method
			@sa http://book.cakephp.org/2.0/en/models/callback-methods.html#beforesave
		*/
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

		//! If membership has just been revoked, clear all Group records for the Member.
		/*!
			Privileges are tied to groups, so we need to make sure that a former Member is no-longer
			a part of any Group.

			@param int $id The ID of the Member record.
			@param array $newData The new member details.
		*/
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

		//! If the Member has just had their MemberStatus set to 'current member' then we need to make sure they're a member of the 'current members' Group.
		/*!
			Privileges are tied to groups, so any Member that has the 'current member' MemberStatus should be in the 'current members' Group
			so that they have the correct permissions.

			@param int $id The ID of the Member record.
			@param array $newData The new member details.
		*/
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

		//! Checks to see if a specific Member is part of a specific Group.
		/*!
			@param int $memberId The ID of the member record.
			@param int $groupId The ID of the Group record we're checking for.
		*/
		public function memberInGroup($memberId, $groupId)
		{
			return in_array($groupId, Hash::extract( $this->find('first', array( 'conditions' => array( 'Member.member_id' => $memberId ) ) ), 'Group.{n}.grp_id' ));
		}

		//! Add an extra validation rule to the e-mail field stating that the user supplied e-mail must match what's in the database.
		/*!
			@sa Member::checkEmailMatch()
			@sa Member::removeEmailMustMatch()
		*/
		public function addEmailMustMatch()
		{
			$this->validator()->add('email', 'emailMustMatch', array( 'rule' => array( 'checkEmailMatch' ), 'message' => 'Incorrect email used' ));
		}

		//! Remove the 'e-mail must match' validation rule.
		/*!
			@sa Member::checkEmailMatch()
			@sa Member::addEmailMustMatch()
		*/
		public function removeEmailMustMatch()
		{
			$this->validator()->remove('email', 'emailMustMatch');
		}

		//! Function to check to see if a Member is in a Group without hitting the database.
		/*!
			@param array $member The Member record to check.
			@param int $groupId The Group ID to check against.
			@retval bool True if the Member is in the Group, false otherwise.
		*/
		public static function isInGroup($member, $groupId)
		{
			if(	isset($member) &&
				isset($member['Group']))
			{
				return in_array($groupId, Hash::extract($member['Group'], '{n}.grp_id'));
			}

			return false;
		}

		//! Test to see if a Member is in the 'full access' Group without hitting the database
		/*!
			@param array $member The Member record to check.
			@retval bool True if the Member is in the 'full access' Group, false otherwise.
		*/
		public static function isInGroupFullAccess($user)
		{
			return Member::isInGroup($user, 1);
		}

		//! Test to see if a Member is in the 'member admin' Group without hitting the database
		/*!
			@param array $member The Member record to check.
			@retval bool True if the Member is in the 'member admin' Group, false otherwise.
		*/
		public static function isInGroupMemberAdmin($user)
		{
			return Member::isInGroup($user, 5);
		}

		//! Test to see if a Member is in the 'tour guide' Group without hitting the database
		/*!
			@param array $member The Member record to check.
			@retval bool True if the Member is in the 'tour guide' Group, false otherwise.
		*/
		public static function isInGroupTourGuide($user)
		{
			return Member::isInGroup($user, 6);
		}

		//! Find how many members have a certain Status.
		/*!
			@param int $status_id The id of the Status record to check.
			@retval int The number of member records that belong to the Status.
		*/
		public function getCountForStatus($status_id)
		{
			return $this->find( 'count', array( 'conditions' => array( $this->belongsTo['Status']['foreignKey'] => $status_id ) ) );
		}

		//! Find out how many member records exist in the database.
		/*!
			@retval int The number of member records in the database.
		*/
		public function getCount()
		{
			return $this->find( 'count' );
		}
	}
?>