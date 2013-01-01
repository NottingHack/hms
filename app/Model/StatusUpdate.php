<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model for all status update data
	 *
	 *
	 * @package       app.Model
	 */
	class StatusUpdate extends AppModel 
	{	
		public $useTable = 'status_updates'; //!< Specify table.
		public $primaryKey = 'id'; //!< Specify primary key.

		//! We belong to two Members and two Statuses.
		/*!
			Member is the Member the status update was performed on.
			MemberAdmin is the Member that performed the update status action.
			OldStatus is the previous Status of Member.
			NewStatus is the current Status of Member.
		*/
	    public $belongsTo = array(
	    	'Member' => array(
				'className' => 'Member',
				'foreignKey' => 'member_id'
			),
			'MemberAdmin' => array(
				'className' => 'Member',
				'foreignKey' => 'admin_id',
			),
			'OldStatus' => array(
				'className' => 'Status',
				'foreignKey' => 'old_status',
			),
			'NewStatus' => array(
				'className' => 'Status',
				'foreignKey' => 'new_status',
			),
    	);
	}
?>