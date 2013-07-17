<?php
/**
 * Model for all Ideas in MemberVoice
 *
 *
 * @package       plugin.MemberVoice.Model
 */
class MVCategory extends MemberVoiceAppModel {
	public $useTable = 'categories';
	public $alias = 'Category';
	
	public $hasAndBelongsToMany = array(
		'Idea'	=>	array(
				'className'	=>	'MemberVoice.MVIdea',
				'joinTable'	=>	'mv_categories_ideas',
			)
		);
}

?>