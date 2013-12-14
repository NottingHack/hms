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
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Model to handle data and queries for permissions.
 */
class Permission extends AppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'permissions';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'permission_code';
}