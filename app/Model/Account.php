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

		public function generate_payment_ref()
		{
			# Payment ref is a randomly generates string of 'safechars'
			# Stolen from London Hackspace code
			$safeChars = "2346789BCDFGHJKMPQRTVWXY";

			# We prefix the ref with a string that lets people know it's us
			$prefix = 'HSNOTTS';

			# Payment references can be up to 18 chars according to: http://www.bacs.co.uk/Bacs/Businesses/BacsDirectCredit/Receiving/Pages/PaymentReferenceInformation.aspx
			$maxRefLength = 18;

			$paymentRef = '';
			do
			{
				$paymentRef = $prefix;
				for($i = strlen($prefix); $i < $maxRefLength; $i++)
				{
					$paymentRef .= $safeChars[ rand(0, strlen($safeChars) - 1) ];
				}
			} while( $this->find('count', array( 'conditions' => array( 'Account.payment_ref' =>  $paymentRef) )) > 0 );

			return $paymentRef;
		}
	}
?>