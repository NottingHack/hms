<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all member data
	 *
	 *
	 * @package       app.Model
	 */
	class Member extends AppModel 
	{

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
	            'className' => 'Pin',
	            'dependent' => true
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
	    	),
	    );

	    //! We have and belong to many Group.
	    /*!
	    	Group is set to be unique as it is impossible for the Member to be in the same Group twice.
	    	We also specify a model to use as the 'with' model so that we can add methods to it.
	    */
		public $hasAndBelongsToMany = array(
	        'Group' =>
	            array(
	                'className' => 'Group',
	                'joinTable' => 'member_group',
	                'foreignKey' => 'member_id',
	                'associationForeignKey' => 'grp_id',
	                'unique' => true,
	                'with' => 'GroupsMember',				
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
			@retval bool True if the supplied username exists in the database (case-insensitive) registered to a different user, otherwise false.
		*/
		public function checkUniqueUsername($check)
		{
			$lowercaseUsername = strtolower($check);
			$records = $this->find('all', 
				array(  'fields' => array('Member.username'),
					'conditions' => array( 
						'Member.username LIKE' => $lowercaseUsername,
						'Member.member_id NOT' => $this->data['Member']['member_id'],
					) 
				)
			);

			foreach ($records as $record) 
			{
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
		public function beforeSave($options = array()) 
		{
			# Must never ever ever alter the balance
			unset( $this->data['Member']['balance'] );

			return true;
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

		//! Find out if we have record of a Member with a specific e-mail address.
		/*!
			@param string $email E-mail address to check.
			@retval bool True if there is a Member with this e-mail, false otherwise.
		*/
		public function doesMemberExistWithEmail($email)
		{
			return $this->find( 'count', array( 'conditions' => array( 'Member.email' => strtolower($email) ) ) ) > 0;
		}

		//! Get a summary of the member records for all members.
		/*!
			@retval array A summary of the data of all members.
			@sa Member::_getMemberSummary()
		*/
		public function getMemberSummaryAll($paginate)
		{
			return $this->_getMemberSummary($paginate);
		}

		//! Get a summary of the member records for all members.
		/*!
			@param int $statusId Retrieve information about members who have this status.
			@retval array A summary of the data of all members of a status.
			@sa Member::_getMemberSummary()
		*/
		public function getMemberSummaryForStatus($paginate, $statusId)
		{
			return $this->_getMemberSummary($paginate, array( 'Member.member_status' => $statusId ) );
		}

		//! Get a summary of the member records for all member records where their name, email, username or handle is similar to the keyword.
		/*!
			@param string $keyword Term to search for.
			@retval array A summary of the data of all members who match the query.
			@sa Member::_getMemberSummary()
		*/
		public function getMemberSummaryForSearchQuery($paginate, $keyword)
		{
			return $this->_getMemberSummary( $paginate,
				array( 'OR' => 
					array(
						"Member.name Like'%$keyword%'", 
						"Member.email Like'%$keyword%'",
						"Member.username Like'%$keyword%'",
						"Member.handle Like'%$keyword%'",
					)
				)
			);
		}

		//! Format member information into a nicer arrangement.
		/*!
			@param $info The info to format, usually retrieved from Member::_getMemberSummary.
			@retval array An array of member information, formatted so that nothing needs to know database rows.
			@sa Member::_getMemberSummary
		*/
		public function formatMemberInfo($info)
		{
			/*
	    	    Data should be presented to the view in an array like so:
	    			[n] => 
	    				[id] => member id
	    				[name] => member name
	    				[email] => member email
	    				[groups] => 
	    					[n] =>
	    						[id] => group id
	    						[description] => group description
	    				[status] => 
	    					[id] => status id
	    					[name] => name of the status
	    	*/
			$formattedInfo = array();
	    	foreach ($info as $member) 
	    	{
	    		$id = Hash::get($member, 'Member.member_id');
	    		$name = Hash::get($member, 'Member.name');
	    		$email = Hash::get($member, 'Member.email');

	    		$status = array(
	    			'id' => Hash::get($member, 'Status.status_id'),
	    			'name' => Hash::get($member, 'Status.title'),
	    		);

	    		$groups = array();
	    		foreach($member['Group'] as $group)
	    		{
	    			array_push($groups,
		    			array(
		    				'id' => Hash::get($group, 'grp_id'),
		    				'description' => Hash::get($group, 'grp_description'),
		    			)
		    		);
	    		}

	    		array_push($formattedInfo,
	    			array(
	    				'id' => $id,
	    				'name' => $name,
	    				'email' => $email,
	    				'groups' => $groups,
	    				'status' => $status,
	    			)
	    		);
	    	}

	    	return $formattedInfo;
		}

		//! Create a member info array for a new member.
		/*!
			@param string $email The e-mail address for the new member.
			@retval array An array of member info suitable for saving.
		*/
		public function createNewMemberInfo($email)
		{
			return array(
				'Member' => array(
					'email' => $email,
					'member_status' => Status::PROSPECTIVE_MEMBER,
				),
			);
		}

		//! Get the Status for a member, may hit the database.
		/*!
			@param mixed $memberData If array, assumed to be an array of member info in the same format that is returned from database queries, otherwise assumed to be a member id.
			@retval int The status for the member, or 0 if status could nto be found.
		*/
		public function getStatusForMember($memberData)
		{
			if(!isset($memberData))
			{
				return 0;
			}

			if(is_array($memberData))
			{
				$status = Hash::get($memberData, 'Member.member_status');
				if(isset($status))
				{
					return $status;
				}
				else
				{
					$memberData = Hash::get($memberData, 'Member.member_id');
				}
			}

			$memberInfo = $this->find('first', array('fields' => array('Member.member_status'), 'conditions' => array('Member.member_id' => $memberData) ));
			if(is_array($memberInfo))
			{
				$status = Hash::get($memberInfo, 'Member.member_status');
				if(isset($status))
				{
					return (int)$status;
				}
			}

			return 0;
		}

		//! Get a list of e-mail addresses for all members in a Group.
		/*!
			@param int $groupId The id of the group the members must belong to.
			@retval array A list of member e-mails.
		*/
		public function getEmailsForMembersInGroup($groupId)
		{
			$memberIds = $this->GroupsMember->getMemberIdsForGroup($groupId);
			if(count($memberIds) > 0)
			{
				$emails = $this->find('all', array('fields' => array('email'), 'conditions' => array('Member.member_id' => $memberIds)));
				return Hash::extract( $emails, '{n}.Member.email' );
			}
			return array();
		}

		//! Attempt to register a new member record.
		/*!
			@param array $data Information to use to create the new member record.
			@retval mixed Array of details if the member record was created or didn't need to be, or null if member record could not be created.
		*/
		public function tryRegisterMember($data)
		{
			if(!isset($data) || !is_array($data))
			{
				return null;
			}

			if( (isset($data['Member']) && isset($data['Member']['email'])) == false )
			{
				return null;
			}

			$this->set($data);

			// Need to validate only the e-mail
			if( !$this->validates( array( 'fieldList' => array( 'email' ) ) ) )
			{
				// Failed
				return null;
			}

			// Grab the e-mail
			$email = $data['Member']['email'];

			// Start to build the result data
			$resultDetails = array();
			$resultDetails['email'] = $email;

			// Do we already know about this e-mail?
			$memberInfo = $this->findByEmail( $email );

			// Find only returns an array if it was successful
			$newMember = !is_array($memberInfo);
			$resultDetails['createdRecord'] = $newMember;

			$memberId = -1;
			if($newMember)
			{
				$memberInfo = $this->createNewMemberInfo( $email );

				if( $this->_saveMemberData( $memberInfo, array( 'member_id', 'email', 'member_status' ) ) != true )
				{
					// Save failed for reasons.
					return null;
				}

				$memberId = $this->id;
			}
			else
			{
				$memberId = Hash::get($memberInfo, 'Member.member_id');
			}
			
			$resultDetails['status'] = (int)$this->getStatusForMember( $memberInfo );
			$resultDetails['memberId'] = $memberId;

			// Success!
			return $resultDetails;
		}

		//! Create or save a member record, and all associated data.
		/*! 
			@param array $memberInfo The information to use to create or update the member record.
			@param array $fields The fields that should be saved.
			@retval boolean True if data has been saved successfully, false otherwise.
		*/
		private function _saveMemberData($memberInfo, $fields)
		{
			$dataSource = $this->getDataSource();
			$dataSource->begin();

			$memberId = Hash::get( $memberInfo, 'Member.member_id' );

			// If the member already exists, sort out the groups
			if($memberId != null)
			{
				$newStatus = (int)$this->getStatusForMember( $memberInfo );

				if( $newStatus == Status::CURRENT_MEMBER )
				{
					// Member must be in the current member group
					if( !$this->GroupsMember->addMemberToGroup( $memberId, Group::CURRENT_MEMBERS ) )
					{
						$dataSource->rollback();
						return false;
					}
				}
				else
				{
					// Member has to be stripped of all group info
					if( !$this->GroupsMember->removeAllGroupsForMember( $memberId ) )
					{
						$dataSource->rollback();
						return false;
					}
				}
			}
			else
			{
				$this->Create();
			}

			if( $this->saveAll( $memberInfo, array( 'fieldList' => $fields ), $fields) == false )
			{
				$dataSource->rollback();
				return false;
			}

			// We're good
			$dataSource->commit();
			return true;
		}


		//! Get a summary of the member records for all members that match the conditions.
		/*!
			@retval array A summary (id, name, email, Status and Groups) of the data of all members that match the conditions.
		*/
		private function _getMemberSummary($paginate, $conditions = array())
		{
			$findOptions = array('conditions' => $conditions);

			if($paginate)
			{
				return $findOptions;
			}

			$info = $this->find( 'all', $findOptions );

			return $this->formatMemberInfo($info);
		}
	}
?>