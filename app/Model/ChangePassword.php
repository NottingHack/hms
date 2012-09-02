<?php
	class ChangePassword extends AppModel {
		
		public $useTable = false; # This is a dummy model so we can get nice validation for the change_password view

	    public $validate = array(
	        'current_password' => array(
	            'rule' => 'notEmpty'
	        ),
	        'new_password' => array(
	        	'rule' => 'notEmpty'
	        ),
	        'new_password_confirm' => array(
	        	'noEmpty' => array(
	            	'rule' => 'notEmpty',
	            	'message' => 'This field cannot be left blank'
	            ),
	        	'matchNewPassword' => array(
	            	'rule' => array( 'newPasswordConfirmMatchesNewPassword' ),
	            	'message' => 'Passwords don\'t match',
	            )
	        )
	    );

		public function newPasswordConfirmMatchesNewPassword($check)
		{
			return $this->data['ChangePassword']['new_password'] == $this->data['ChangePassword']['new_password_confirm'];
		}
	}
?>