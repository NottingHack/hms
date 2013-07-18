<?php
/**
 * Model for all Comments in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MVComment extends MemberVoiceAppModel {
	public $useTable = 'comments';
	public $alias = 'Comment';
	public $belongsTo = array();

	public function __construct() {
		$this->belongsTo['User'] = array(
										'className'		=>	$this->mvUserModel,
										'foreignKey'	=>	'user_id',
										);


		parent::__construct();
	}
}

?>