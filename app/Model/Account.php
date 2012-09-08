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

		public function generate_payment_ref($memberInfo)
		{
			# Payment ref is a randomly generates string of 'safechars'
			# Stolen from London Hackspace code
			$safeChars = "2346789BCDFGHJKMPQRTVWXY";
			# So we want 8 of these chars in two groups of 4
			# Then we need to make sure it's unique
			$numBlocks = 2;
			$numCharsInBlock = 4;

			$paymentRef = "";
			do
			{
				$paymentRef = "";
				for($b = 0; $b < $numBlocks; $b++)
				{
					for($c = 0; $c < $numCharsInBlock; $c++)
					{
						$paymentRef .= $safeChars[ rand(0, strlen($safeChars)) ];
					}

					if($b < ($numBlocks - 1))
					{
						$paymentRef .= '-';
					}
				}
			} while( $this->find('count', array( 'conditions' => array( 'Account.payment_ref' =>  $paymentRef) )) > 0 );

			return $paymentRef;
		}
	}
?>