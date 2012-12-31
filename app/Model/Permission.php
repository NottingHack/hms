<?php

	App::uses('AppModel', 'Model');

	/**
	 * Model to handle data and queries for permissions.
	 *
	 *
	 * @package       app.Model
	 */
	class Permission extends AppModel 
	{	
		public $useTable = "permissions";	//!< Specify the table to use.
		public $primaryKey = 'permission_code'; //!< Specify the primary key to use.
	}
?>