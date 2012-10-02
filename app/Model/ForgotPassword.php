<?php
	class ForgotPassword extends AppModel {
		
		public $useTable = 'forgotpassword';

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

	    public $primaryKey = 'request_guid';

	    public function newPasswordConfirmMatchesNewPassword($check)
		{
			return $this->data['ForgotPassword']['new_password'] == $this->data['ForgotPassword']['new_password_confirm'];
		}

		public function findMemberWithEmail($check)
		{
			$member = ClassRegistry::init('Member');
			$memberCount = $member->find('count', array('conditions' => array('Member.email' => $this->data['ForgotPassword']['email'])));
			return $memberCount > 0;
		}

		public function generate_entry($memberInfo)
		{
			if($memberInfo)
			{
				$data['ForgotPassword']['member_id'] = $memberInfo['Member']['member_id'];
				$data['ForgotPassword']['request_guid'] = String::UUID();
				# AT [30/09/2012] Timestamp is generated automatically
				if($this->save($data))
				{
					return $data;
				}
			}
			return false;
		}
	}
?>