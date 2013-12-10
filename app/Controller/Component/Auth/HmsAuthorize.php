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
 * @package       app.Controller.Component.Auth
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('BaseAuthorize', 'Controller/Component/Auth');

/**
 * HmsAuthorize is basically the same as the CakPHP ControllerAuthorize
 * in that it calls the 'isAuthorized' method of the controller. However
 * HmsAuthorize doesn't discard the CakeRequest object, as it is used in the
 * controller instead of $this->request.
 *
 * @package app.Controller.Component.Auth
 */
class HmsAuthorize extends BaseAuthorize {

/**
 * Accessor to the controller object.
 *
 * @param Controller $controller null to get, a controller to set.
 * @return mixed
 * @throws CakeException if controller is non-null but does not have a function called 'isAuthorized'.
 */
	public function controller(Controller $controller = null) {
		if ($controller) {
			if (!method_exists($controller, 'isAuthorized')) {
				throw new CakeException(__d('cake_dev', '$controller does not implement an isAuthorized() method.'));
			}
		}
		return parent::controller($controller);
	}

/**
 * Check if the user is authorized to access the URL specified in the request.
 * @param  array $user Array of user data.
 * @param  CakeRequest $request The request the user is attempting to make.
 * @return bool True if user is authorized to perform the request, false otherwise.
 */
	public function authorize($user, CakeRequest $request) {
		return (bool)$this->_Controller->isAuthorized($user, $request);
	}

}
