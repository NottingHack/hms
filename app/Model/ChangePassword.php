<?php

	App::uses('Member', 'Model');

	class ChangePassword extends AppModel {
		
		public $useTable = false; # This is a dummy model so we can get nice validation for the change_password view

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

		public function newPasswordConfirmMatchesNewPassword($check)
		{
			return $this->data['ChangePassword']['new_password'] == $this->data['ChangePassword']['new_password_confirm'];
		}
	}
?>