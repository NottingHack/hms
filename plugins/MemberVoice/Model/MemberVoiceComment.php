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
 * Model for all comments data.
 */
class MemberVoiceComment extends MemberVoiceAppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'comments';

/**
 * Specify a nicer alias for this model.
 * @var string
 */
	public $alias = 'Comment';

/**
 * Specify the 'belongs to' associations. (Populated in the ctor because we need to access parent properties).
 * @var array
 */
	public $belongsTo = array();

/**
 * Constructor.
 *
 * We need to build the belongsTo array within the constructor,
 * because we need to access properties in the parent object.
 */
	public function __construct() {
		// Make sure we call the parent constructor
		parent::__construct();

		$this->belongsTo['User'] = array(
			'className'	=> $this->mvUserModel,
			'foreignKey' => 'user_id',
		);
	}
}