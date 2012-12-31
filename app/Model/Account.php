<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all group data
	 *
	 *
	 * @package       app.Model
	 */
	class Account extends AppModel 
	{
		public $useTable = "account";	//!< Specify the table to use.

		public $primaryKey = 'account_id';	//!< Specify the primary key to use.

		//! An Account can have many Member
		public $hasMany = array(
	        'Member' => array(
	            'className'    => 'Member',
	        ),
	    );

		//! Validation rules.
		/*!
			Account Reference must not be empty.
		*/
	    public $validate = array(
	        'account_ref' => array(
	            'rule' => 'notEmpty'
	        ),
	    );

	    //! Generate a unique payment reference
	    /*!
	    	@retval string A unique (at the time of function-call) payment reference.
	    	@sa http://www.bacs.co.uk/Bacs/Businesses/BacsDirectCredit/Receiving/Pages/PaymentReferenceInformation.aspx
	    */
		public function generatePaymentRef()
		{
			// Payment ref is a randomly generates string of 'safechars'
			// Stolen from London Hackspace code
			$safeChars = "2346789BCDFGHJKMPQRTVWXY";

			// We prefix the ref with a string that lets people know it's us
			$prefix = 'HSNOTTS';

			// Payment references can be up to 18 chars according to: http://www.bacs.co.uk/Bacs/Businesses/BacsDirectCredit/Receiving/Pages/PaymentReferenceInformation.aspx
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