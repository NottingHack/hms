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
 * @package       plugins.Tools.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('HmsAuthenticate', 'Controller/Component/Auth');
App::uses('Member', 'Model');
App::uses('Tools.ToolsTool', 'Model');

/**
 * Base controller for all controllers in the Tools plugin.
 */
class ToolsAppController extends AppController {

/**
 * Returns the userID of the currently logged in user
 * 
 * @return mixed The userID as defined by the containing application
 */
	protected function _getUserID() {
		return $this->_getLoggedInMemberId();
	}

/**
 * Check to see if a user is authorized to perform an action.
 * @param  array $user Array of user data
 * @param  CakeRequest $request The request the user is attempting to perform.
 * @return bool True if the user is authorized to perform the action, false otherwise.
 */
	public function isAuthorized($user, $request) {
		// pass all requests to the parent.
		// This will allow "full access" users to access everything
		if (parent::isAuthorized($user, $request)) {
			return true;
		}

		// non-logged in users can never access
		$memberId = $this->Member->getIdForMember($user);

		if ($memberId <= 0) {
			return false;
		}

		// Logged in users will be redirected to the index page

		$this->Auth->unauthorizedRedirect = array(
			'plugin'		=> 'Tools',
			'controller'	=> 'ToolsTools',
			'action'		=> 'index',
			);

		// Array of all request in this plugin that any logged in user can access
		$allowedRequests = array(
								'ToolsTools' => array(
													 'index',
													 ),
								);

		// Array of all requests in this plugin that may be restricted
		$restrictedRequests = array(
									'ToolsTools' =>	array(
														  'view',
														  'addbooking',
														  )
									);

		if ($this->request->params['plugin'] == 'Tools') {
			// is it a general page?
			if (array_key_exists($this->request->params['controller'], $allowedRequests)) {
				if (in_array($this->request->params['action'], $allowedRequests[$this->request->params['controller']])) {
					return true;
				}
			}

			// Is this user allowed to access this page?
			if (array_key_exists($this->request->params['controller'], $restrictedRequests)) {
				if (in_array($this->request->params['action'], $restrictedRequests[$this->request->params['controller']])) {
					return $this->ToolsTool->isUserInducted($this->request->params['pass'][0], $memberId);
				}
			}
		}

		return false;
	}
}