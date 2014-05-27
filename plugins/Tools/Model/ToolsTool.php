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
 * @package       plugins.Tools.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Model for all tool data.
 */
class ToolsTool extends ToolsAppModel {

	/**
	 * Specify the table to use.
	 *
	 * @var string
	 */
	public $useTable = 'tools';

	/**
	 * Uses a different primary key
	 *
	 * @var int
	 */
	public $primaryKey = 'tool_id';

	/**
	 * Specify a nicer alias for this model.
	 *
	 * @var string
	 */
	public $alias = 'Tool';

	/**
	 * The validation rules
	 *
	 * @var array
	 */
	public $validate = array(
		'tool_name' => array(
			'rule'    => 'isUnique',
			'message' => 'This tool name has already been taken.'
			),
		); 

	public function isUserInducted($toolId) {
		return true;
	}

	public function isUserAnInductor($toolId) {
		return false;
	}

	public function isUserAMaintainer($toolId) {
		return false;
	}
}

?>