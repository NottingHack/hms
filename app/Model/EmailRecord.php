<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all pin data
	 *
	 *
	 * @package       app.Model
	 */
	class EmailRecord extends AppModel 
	{
		public $useTable = 'hms_emails'; 		//!< Specify the table to use.
		public $primaryKey = 'hms_email_id';	//!< Specify the promary key to use.

	    //! Validation rules.
	    public $validate = array(
	    	'member_id' => array(
	        	 'length' => array(
	        		'rule' => array('maxLength', 11),
	        		'message' => 'Member id must be no more than 11 characters long',
	        	),
	        	'content' => array(
	        		'rule' => 'numeric',
	        		'message' => 'Member id must be a number',
	        	),
	        ),
	        'subject' => array(
	            'rule' => 'notEmpty',
	            'message' => 'Must have a subject',
	        ),
	    );

		//! Create one or more new EmailRecord entry
		/*!
			@param mixed $to Either a single member_id or an array of member id's that the e-mail was sent to.
			@param string $subject The subject of the e-mail.
			@retval bool True if creation was successful, false otherwise.
		*/
		public function createNewRecord($to, $subject)
		{
			if(!isset($to) || !isset($subject))
			{
				return false;
			}

			if(!is_string($subject))
			{
				return false;
			}

			if(is_array($to))
			{
				// Creating multiple records, wrap it up in a transaction
				$dataSource = $this->getDataSource();
				$dataSource->begin();
				foreach ($to as $id) 
				{
					if(!$this->_createNewRecordImpl($id, $subject))
					{
						$dataSource->rollback();
						return false;
					}
				}
				$dataSource->commit();
				return true;
			}
			else if(is_numeric($to))
			{
				return $this->_createNewRecordImpl($to, $subject);
			}

			return false;
		}

		//! Create a new EmailRecord entry
		/*!
			@param int $to The member_id of the member that the e-mail was sent to.
			@param string $subject The subject of the e-mail.
			@retval bool True if creation was successful, false otherwise.
		*/
		private function _createNewRecordImpl($to, $subject)
		{
			$this->Create();

			$data = 
			array( 'EmailRecord' => 
				array(
					'member_id' => $to,
					'subject' => $subject,
					'timestamp' => date('Y-m-d H:i:s'),
				)
			);

			return ($this->save($data) != false);
		}
	}
?>