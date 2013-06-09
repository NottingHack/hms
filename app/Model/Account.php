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

		//! An Account has many Member
		public $hasMany = array(
	        'Member' => array(
	            'className'    => 'Member',
	        ),
	    );

		//! Validation rules.
		/*!
			Account Id must be numeric.
			Payment Reference must not be empty.
		*/
	    public $validate = array(
	    	'account_id' => array(
	        	'rule' => 'numeric',
	        ),
	        'payment_ref' => array(
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
			$maxRefLength = 16;

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

		//! Create and save a new account if needed or check for an existing account.
		/*!
			@param int An account id.
			@retval int The new or existing account id on success, or -1 on error.
		*/
		public function setupAccountIfNeeded($accountId)
		{
			if(isset($accountId))
			{
				if($accountId <= 0)
				{
					// Generate a new account
					$this->Create();
					$data = array(
						'Account' => array(
							'payment_ref' => $this->generatePaymentRef(),
						),
					);

					if( $this->save($data, array('fieldList' => array('account_id', 'payment_ref'))) )
					{
						// New account is ok
						$accountId = $this->getID();
						return $accountId;
					}
					
					// New account creation failed
					return -1;
				}
				else
				{
					// Attempt to find an account with this id
					if( $this->find('first', array('conditions' => array('account_id' => $accountId))) )
					{
						// All good
						return $accountId;
					}
				}				
			}
			return -1;
		}

		//! Get a list of account ids from a list of payment refs
		/*!
			@param array $paymentRefs An array of payment refs.
			@retval array An array of account ids.
		*/
		public function getAccountIdsForRefs($paymentRefs)
		{
			if(is_array($paymentRefs))
			{
				return array_values($this->find('list', array('conditions' => array('Account.payment_ref' => $paymentRefs))));
			}

			return array();
		}
	}
?>