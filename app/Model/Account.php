<?php
	class Account extends AppModel {
		
		public $useTable = "account";

		public $primaryKey = 'account_id';

		public $hasMany = array(
	        'Member' => array(
	            'className'    => 'Member',
	        ),
	    );

	    public $validate = array(
	        'account_ref' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

		public static function generate_payment_ref($memberInfo)
		{
			# Payment ref is pin followed by as much of the name as possible
			$pin = $memberInfo['Pin']['pin'];
			$fullName = $memberInfo['Member']['name'];
			$ref = $pin . str_replace(" ", "_", substr($fullName, 0, 8));
			return $ref;
		}
	}
?>