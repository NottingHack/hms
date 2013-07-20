<?php
class MemberVoiceAppModel extends AppModel {
	// All our tables are kept seperate
	public $tablePrefix = "mv_";

	// Knows the model name of the external app
	public $mvUserModel = 'Member';
}
?>