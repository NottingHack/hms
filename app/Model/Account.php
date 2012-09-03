<?php
	class Account extends AppModel {
		
		public $useTable = "account";

		public $primaryKey = 'account_id';

    	public $belongsTo =  array(
			"Member" => array(
					"className" => "Member",
					"foreignKey" => "Member_id",
					"type" => "inner"
			),
		);

	    public $validate = array(
	        'account_ref' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

		public static function generate_payment_ref($memberInfo)
		{
			# Currently a PIN is a 4 digit number between 1000 and 9999
			$fullName = $memberInfo['Member']['name'];

			$ref = substr($fullName, 0, 12);
			$ref = str_replace(" ", "_", $ref);
			return $ref;
		}
	}
?>