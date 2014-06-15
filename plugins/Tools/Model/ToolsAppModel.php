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
 * Base model for all models in the Tools plugin.
 */
class ToolsAppModel extends AppModel {

/**
 * Prefix for the MemberVoice tables.
 * @var string
 */
	public $tablePrefix = "tl_";

/**
 * Name of the model in the app that contains user data.
 * @var string
 */
	public $tlUserModel = 'Member';
}