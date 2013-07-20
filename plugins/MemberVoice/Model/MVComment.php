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

	//! We need to build the belongsTo array within the constructor
	/*!
		We need to access properties in the parent object within the belongsTo array
	*/
	public function __construct() {
		$this->belongsTo['User'] = array(
										'className'		=>	$this->mvUserModel,
										'foreignKey'	=>	'user_id',
										);

		// Make sure we call the parent constructor
		parent::__construct();
	}
}

?>