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
 * Model for all account data
 */
class Bank extends AppModel {

/**
 * Specify the table to use
 * @var string
 */
//	public $useTable = 'bank_transactions';

/**
 * Specify the primary key to use.
 * @var string
 */
	public $primaryKey = 'bank_id';	//!< Specify the primary key to use.

}