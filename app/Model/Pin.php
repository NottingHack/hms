<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all pin data
	 *
	 *
	 * @package       app.Model
	 */
	class Pin extends AppModel 
	{	
		public $useTable = 'pins';	//!< Specify the table to use.
		public $primaryKey = 'pin_id';	//!< Specify the promary key to use.

		//! We belong to a Member.
		/*!
			Member join type is inner as it makes no sense to have a pin that has no Member.
		*/
	    public $belongsTo = array(
	    	'Member' => array(
				'className' => 'Member',
				'foreignKey' => 'member_id',
				'type' => 'inner'
			)
    	);

	    //! Validation rules.
	    /*!
	    	Pin must not be empty.
	    	Member Id must not be empty.
	    	Unlock Text must not be empty.
	    */
	    public $validate = array(
	        'pin' => array(
	            'rule' => 'notEmpty'
	        ),
	        'member_id' => array(
	        	'rule' => 'notEmpty'
	        ),
	        'unlock_text' => array(
	            'rule' => 'notEmpty'
	        )
	    );

	    //! Generate a random pin.
	    /*!
	    	@retval int A random pin.
	    */
		public static function generatePin()
		{
			# Currently a PIN is a 4 digit number between 1000 and 9999
			return rand(1000, 9999);
		}


		//! Generate a unique (at the time this function was called) pin.
		/*!
			@retval int A random pin that was not in the database at the time this function was called.
		*/
		public function generateUniquePin()
		{
			# A loop hiting the database? Why not...
			$pin = 0;
			do
			{
				$pin = Pin::generatePin();
			} while ( $this->find( 'count', array( 'conditions' => array( 'Pin.pin' => $pin ) ) ) > 0 );

			return $pin;
		}
	}
?>