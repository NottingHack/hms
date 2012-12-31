<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model to provide validation for the change password form/view.
	 *
	 *
	 * @package       app.Model
	 */
	class ChangePassword extends AppModel 
	{
		public $useTable = false; //!< Don't use any table, this is just a dummy view.

		//! Validation rules.
		/*!
			Current Password must not be empty.
			New Password must not be empty, and must be at-least Member::MIN_PASSWORD_LENGTH characters.
			New Password Confirm must not be empty, must match New Password and must be at-least Member::MIN_PASSWORD_LENGTH characters.
		*/
	    public $validate = array(
	        'current_password' => array(
	            'rule' => 'notEmpty'
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
	    	@param string $check New Password Confirm supplied by the user.
	    	@retval bool True if the two passwords match, false otherwise.
	    */
		public function newPasswordConfirmMatchesNewPassword($check)
		{
			return $this->data['ChangePassword']['new_password'] === $check;
		}
	}
?>