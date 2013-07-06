<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MVIdea extends MemberVoiceAppModel {
	public $useTable = 'ideas';
	public $name = "Idea";
	
	public $hasAndBelongsToMany = array(
		'Category'	=>	array(
				'className'	=>	'MemberVoice.MVCategory',
				'joinTable'	=>	'mv_categories_ideas',
			)
		);
	public $hasMany = array(
		'Vote'	=>	array(
				'className'	=>	'MemberVoice.MVVote',
			),
		'Comment'	=>	array(
				'className'	=>	'MemberVoice.MVComment',
			),
		);
	public $belongsTo = array(
		'Status'	=>	array(
				'className'	=>	'MemberVoice.MVStatus',
			)
		);
}

?>