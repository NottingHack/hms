<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model to handle data and requests for the forgot password process.
	 *
	 *
	 * @package       app.Model
	 */
	class ForgotPassword extends AppModel 
	{
		public $useTable = 'forgotpassword'; //!< Specify the table to use.

		public $primaryKey = 'request_guid'; //!< Specify the primary key.

		//! Validation rules.
		/*!
			Email must not be empty, and must match an e-mail belonging to a Member in the database.
			New Password must not be empty, and must be at-least Member::MIN_PASSWORD_LENGTH characters long.
			New Password Confirm must not be empty, must match New Password and must be at-least Member::MIN_PASSWORD_LENGTH characters long.
		*/
	    public $validate = array(
	        'email' => array(
	            'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	            'matchMemberEmail' => array(
	            	'rule' => array( 'findMemberWithEmail' ),
	            	'message' => 'Cannot find a member with that e-mail',
	            )
	        ),
	        'new_password' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	        	'minLen' => array(
	        		'rule' => array('minLength', Member::MIN_PASSWORD_LENGTH),
            		'message' => 'Password too short',
            	),
	        ),
	        'new_password_confirm' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	        	'matchNewPassword' => array(
	            	'rule' => array( 'newPasswordConfirmMatchesNewPassword' ),
	            	'message' => 'Passwords don\'t match',
	            ),
	            'minLen' => array(
	        		'rule' => array('minLength', Member::MIN_PASSWORD_LENGTH),
            		'message' => 'Password too short',
            	),
	        )
	    );
	
		//! Test to see if the user-supplied New Password Confirm matches the user-supplied New Password.
		/*!
			@param string $check User-supplied New Password Confirm.
			@retval bool True if the passwords match, false otherwise.
		*/
	    public function newPasswordConfirmMatchesNewPassword($check)
		{
			return $this->data['ForgotPassword']['new_password'] === $check;
		}

		//! Test to see if we have a record of a Member with the e-mail the user is asking for.
		/*!
			@param string $check The e-mail address to check.
			@retval bool True if we have record of a Member with that e-mail, otherwise false.
		*/
		public function findMemberWithEmail($check)
		{
			$member = ClassRegistry::init('Member');
			return $member->doesMemberExistWithEmail($check);
		}

		//! Create a new entry in the forgot password database for a Member.
		/*
			@param int $memberId Id of the Member to create the forgot password record for.
			@retval mixed An array of the newly created forgot password record or false if the create failed.
		*/
		public function createNewEntry($memberId)
		{
			$data['ForgotPassword']['member_id'] = $memberId;
			$data['ForgotPassword']['request_guid'] = String::UUID();
			// Timestamp is generated automatically
			return $this->save($data);
		}
	}
?>