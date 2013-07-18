<?php
class MemberVoiceAppModel extends AppModel {
	public $tablePrefix = "mv_";

	/* Knows the model name of the external app */
	public $mvUserModel = 'Member';
}
?>