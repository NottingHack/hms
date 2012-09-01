<?php
	class MemberAuth extends AppModel {

		public $primaryKey = 'member_id';

		public $useTable = "members_auth";

		public $belongsTo =  array(
				"Member" => array(
						"className" => "Member",
						"foreignKey" => "member_id",
						"type" => "inner"
				),
		);
	}
?>