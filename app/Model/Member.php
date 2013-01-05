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
		public function getMemberSummaryAll()
		{
			return $this->_getMemberSummary();
		}

		//! Get a summary of the member records for all members that match the conditions.
		/*!
			@retval array A summary (id, name, email, Status and Groups) of the data of all members that match the conditions.
		*/
		private function _getMemberSummary($conditions = array())
		{
			$info = $this->find( 'all', array('conditions' => $conditions) );

			return $this->_formatMemberInfo($info);
		}

		//! Format member information into a nicer arrangement.
		/*!
			@param $info The info to format, usually retrieved from Member::_getMemberSummary.
			@retval array An array of member information, formatted so that nothing needs to know database rows.
			@sa Member::_getMemberSummary
		*/
		private function _formatMemberInfo($info)
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
	}
?>