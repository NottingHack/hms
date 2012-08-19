<?php
	class Member extends AppModel {

		public $primaryKey = 'member_id';

		public $belongsTo = 
			array(
				"Status" =>
					array(
						"className" => "Status",
						"foreignKey" => "member_status",
						"type" => "inner"
					)
			);

	}
?>