<?php
	
	/**
	 * Model for all member status data
	 *
	 *
	 * @package       app.Model
	 */
	class Status extends AppModel {
		
		//! We use the 'status' table instead of the default.
		public $useTable = "status";

		//! We use 'status_id' as our primary key.
		public $primaryKey = 'status_id';

		//! Get the id, name and description of all statuses.
		/*
			@retval array The id, name and description for all statuses.
		*/
		public function getIdNameDescriptionAll()
		{
			return $this->find('all', array( 'fields' => array( $this->primaryKey, 'title', 'description' ) ) );
		}
	}
?>