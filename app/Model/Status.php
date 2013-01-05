<?php

	App::uses('AppModel', 'Model');
	
	/**
	 * Model for all member status data
	 *
	 *
	 * @package       app.Model
	 */
	class Status extends AppModel 
	{
		const PROSPECTIVE_MEMBER = 1; //!< The id of the prospective member status.
	    const PRE_MEMBER_1 = 2; //!< The id of the pre-member (stage 1) status.
	    const PRE_MEMBER_2 = 3; //!< The id of the pre-member (stage 2) status.
	    const PRE_MEMBER_3 = 4; //!< The id of the pre-member (stage 3) status.
	    const CURRENT_MEMBER = 5; //!< The id of the current member status.
	    const EX_MEMBER = 5; //!< The id of the ex member status.

		public $useTable = "status"; //!< We use the 'status' table instead of the default.
		public $primaryKey = 'status_id'; //!< We use 'status_id' as our primary key.

		//! We have many Member.
	    public $hasMany = array(
	    	'Member' => array(
	    		'foreignKey' => 'member_status',
	    	),
	    );

		//! Get a summary of the status records for all statuses.
		/*!
			@retval array A summary of the data of all statuses.
			@sa Status::_getStatusSummary()
		*/
		public function getStatusSummaryAll()
		{
			return $this->_getStatusSummary();
		}

		//! Get a summary of the status records for a single status.
		/*!
			@param int $id The id of the status to look at
			@retval mixed A summary of the data for a single status, or false if none can be found.
			@sa Status::_getStatusSummary()
		*/
		public function getStatusSummaryForId($id)
		{
			$info = $this->_getStatusSummary( array('Status.status_id' => $id) );

			if(count($info) > 0)
			{
				return $info[0];
			}
			return $info;
		}

		//! Get a summary of the status records for all statuses that match the conditions.
		/*!
			@retval array A summary (id, name, description and member count) of the data of all statuses that match the conditions.
		*/
		private function _getStatusSummary($conditions = array())
		{
			$info = $this->find( 'all', array('conditions' => $conditions) );

			return $this->_formatStatusInfo($info);
		}

		//! Format status information into a nicer arrangement.
		/*!
			@param $info The info to format, usually retrieved from Status::_getStatusSummary.
			@retval array An array of status information, formatted so that nothing needs to know database rows.
			@sa Status::_getStatusSummary
		*/
		private function _formatStatusInfo($info)
		{
			/*
	    	    Data should be presented to the view in an array like so:
	    			[n] => 
	    				[id] => status id
	    				[name] => status name
	    				[description] => status description
	    				[count] => number of members with this status
	    	*/

			$formattedInfo = array();
	    	foreach ($info as $status) 
	    	{
	    		$id = Hash::get($status, 'Status.status_id');
	    		$name = Hash::get($status, 'Status.title');
	    		$description = Hash::get($status, 'Status.description');
	    		$count = count( Hash::extract($status, 'Member') );

	    		array_push($formattedInfo,
	    			array(
	    				'id' => $id,
	    				'name' => $name,
	    				'description' => $description,
	    				'count' => $count,
	    			)
	    		);
	    	}

	    	return $formattedInfo;
		}
	}
?>