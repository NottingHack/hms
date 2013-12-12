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
 * Model to provide validation for the change password form/view.
 */
class ChangePassword extends AppModel {

/**
 * We don't use a table, this model is just for validation.
 * @var boolean
 */
	public $useTable = false;

/**
 * [$validate description]
 * @var array
 */
	public $validate = array(
		'current_password' => array(
			'rule' => 'notEmpty'
		),
		'new_password' => array(
			'noEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank'
			),
			'minLen' => array(
				'rule' => array('minLength', Member::MIN_PASSWORD_LENGTH),
				'message' => 'Password too short',
			),
		),
		'new_password_confirm' => array(
			'noEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'This field cannot be left blank'
			),
			'matchNewPassword' => array(
				'rule' => array( 'newPasswordConfirmMatchesNewPassword' ),
				'message' => 'Passwords don\'t match',
			),
			'minLen' => array(
				'rule' => array('minLength', Member::MIN_PASSWORD_LENGTH),
				'message' => 'Password too short',
			),
		)
	);

/**
 * Test to see if the user-supplied New Password Confirm matches the user-supplied New Password.
 * 
 * @param string $check New Password Confirm supplied by the user.
 * @return bool True if the two passwords match, false otherwise.
 */
	public function newPasswordConfirmMatchesNewPassword($check) {
		return $this->data['ChangePassword']['new_password'] === $check['new_password_confirm'];
	}
}