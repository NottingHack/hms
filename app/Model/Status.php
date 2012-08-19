<?php
	class Status extends AppModel {
		# This uses the 'status' table because typeing 'statuses' causes me pain
		public $useTable = "status";

		public $primaryKey = 'status_id';
	}
?>