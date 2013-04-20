<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model to provide validation for the email members form/view.
	 *
	 *
	 * @package       app.Model
	 */
	class MemberEmail extends AppModel 
	{
		public $useTable = false; //!< Don't use any table, this is just a dummy model.

		//! Validation rules.
		/*!
			Subject must not be empty.
			Message must not be empty.
		*/
	    public $validate = array(
	        'subject' => array(
	            'rule' => 'notEmpty'
	        ),
	        'message' => array(
	        	'rule' => 'notEmpty'
	        ),
	    );

	    //! Get the message from an array of member email data.
	    /*!
	    	@param array $data The array of data to get the message from.
	    	@retvl string The message, or null if it could not be found.
	    */
	    public function getMessage($data)
	    {
	    	if( isset($data) && is_array($data) )
	    	{
	    		return Hash::get($data, 'MemberEmail.message');
	    	}
	    	return null;
	    }
	}
?>