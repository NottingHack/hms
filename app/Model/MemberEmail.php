<?php
	class MemberEmail extends AppModel {
		
		public $useTable = false; # This is a dummy model so we can get nice validation for the email_members_with_status view

	    public $validate = array(
	        'subject' => array(
	            'rule' => 'notEmpty'
	        ),
	        'message' => array(
	        	'rule' => 'notEmpty'
	        ),
	    );
	}
?>