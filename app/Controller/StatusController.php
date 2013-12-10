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
 * @package       app.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Controller to handle Status functionality, currently just shows a list of statuses.
 */
class StatusController extends AppController {

/**
 * Views rendered from this controller will have access to the following helpers.
 * @var array
 */
	public $helpers = array('Html', 'Form');

/** 
 * Test to see if a user is authorized to make a request.
 *
 * @param array $user Member record for the user.
 * @param CakeRequest $request The request the user is attempting to make.
 * @return bool True if the user is authorized to make the request, otherwise false.
 * @link http://api20.cakephp.org/class/cake-request
 */
	public function isAuthorized($user, $request) {
		return true;
	}

/**
 * Show a list of the different statuses
 */
	public function index() {
		$this->set('statuses', $this->Status->find('all'));
	}

}