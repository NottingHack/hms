<?php
	class StatusUpdate extends AppModel {
		
		public $useTable = "status_updates";

		public $primaryKey = 'id';

	    public $belongsTo = array(
	    	"Member" => array(
				"className" => "Member",
				"foreignKey" => "member_id"
			),
			"MemberAdmin" => array(
				"className" => "Member",
				"foreignKey" => "admin_id",
			),
			"OldStatus" => array(
				"className" => "Status",
				"foreignKey" => "old_status",
			),
			"NewStatus" => array(
				"className" => "Status",
				"foreignKey" => "new_status",
			),
    	);
	}
?>