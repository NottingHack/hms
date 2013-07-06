<?php
/**
 * Model for all Comments in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MVComment extends MemberVoiceAppModel {
	public $useTable = 'comments';
	public $belongsTo = array(
		'Member'	=>	array(
				'className'	=>	'Member',
			)
		);
}

?>