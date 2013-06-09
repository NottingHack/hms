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
		const STATE_ACTIVE = 10;    //!< This pin can be used for entry (up until the expiry date), cannot be used to register a card.
	    const STATE_EXPIRED = 20;   //!< Pin has expired and can no longer be used for entry.
	    const STATE_CANCELLED = 30; //!< This pin cannot be used for entry, and has likely been used to activate an RFID card.
	    const STATE_ENROLL = 40;    //!< This pin may be used to enrol an RFID card.

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

		//! Create a new pin record
		/*!
			@param int $memberId The id of the member to create the pin for.
			@retval bool True if creation was successful, false otherwise.
		*/
		public function createNewRecord($memberId)
		{
			if(is_numeric($memberId) && $memberId > 0)
			{
				$this->Create();

				$data = array( 'Pin' => 
					array(
						'unlock_text' => 'Welcome',
						'pin' => $this->generateUniquePin(),
						'state' => Pin::STATE_ENROLL,
						'member_id' => $memberId,
					)
				);

				return ($this->save($data) != false);
			}
			return false;
		}
	}
?>