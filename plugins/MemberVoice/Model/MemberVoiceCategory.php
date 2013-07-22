<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MemberVoiceCategory extends MemberVoiceAppModel {
	public $useTable = 'categories';
	public $alias = 'Category';
	
	public $hasAndBelongsToMany = array(
		'Idea'	=>	array(
				'className'	=>	'MemberVoice.MemberVoiceIdea',
				'joinTable'	=>	'mv_categories_ideas',
			)
		);
}

?>