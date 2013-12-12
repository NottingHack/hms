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
 * Model to provide validation for the email members form/view.
 */
class MemberEmail extends AppModel {

/**
 * Specify the table to use.
 * @var boolean
 */
	public $useTable = false;

/**
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'subject' => array(
			'rule' => 'notEmpty'
		),
		'message' => array(
			'rule' => 'notEmpty'
		),
	);

/**
 * Get the message from an array of member email data.
 * 
 * @param array $data The array of data to get the message from.
 * @return string The message, or null if it could not be found.
 */
	public function getMessage($data) {
		if (isset($data) && is_array($data)) {
			return Hash::get($data, 'MemberEmail.message');
		}
		return null;
	}
}