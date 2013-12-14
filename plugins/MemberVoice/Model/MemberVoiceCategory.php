<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       plugins.MemberVoice.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Model for all category data.
 */
class MemberVoiceCategory extends MemberVoiceAppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'categories';

/**
 * Specify a nicer alias for this model.
 * @var string
 */
	public $alias = 'Category';

/**
 * Specify 'has and belongs to many (HABTM) associations.
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'Idea'	=>	array(
			'className'	=>	'MemberVoice.MemberVoiceIdea',
			'joinTable'	=>	'mv_categories_ideas',
		)
	);
}