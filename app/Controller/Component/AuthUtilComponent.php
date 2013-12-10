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
 * @package       app.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');

/**
 * AuthUtil component allows one to check to see if an action is
 * authorized on any controller. It is slow and it is sad about this.
 * (and its days are numbered)
 * 
 * @package       app.Controller.Component
 */
class AuthUtilComponent extends Component {

/**
 * List of components used by this component.
 * @var array
 */
	public $components = array( 'Auth' );

/**
 * Cached list of created controllers, makes things slighlty more speedy.
 * @var array
 */
	private $__controllers = array();

/**
 * The current controller this component is attached to.
 * @var [type]
 */
	private $__currentController = null;

/**
 * Called before the Controller::beforeFilter().
 *
 * @param Controller $controller Controller with components to initialize
 * @link http://book.cakephp.org/2.0/en/controllers/components.html#Component::initialize
 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);
		$this->__currentController = $controller;
	}

/**
 * Check to see if the currently logged in user is authorized to perform
 * an action on a certain controller (with certain params).
 * @param  string $controller The name of the controller the user wants to act on.
 * @param  string $action The action the user wants to perform.
 * @param  array $params Any parameters for the action.
 * @return bool True if user is authorized to perform the action, false otherwise.
 */
	public function isAuthorized($controller, $action, $params = array()) {
		// Build the url and CakeRequest
		$url = '/' . $controller . '/' . $action;

		if (count($params) > 0) {
			$url .= '/' . join($params, '/');
		}

		$request = new CakeRequest($url, false);
		$request->addParams(array(
			'plugin' => null,
			'controller' => $controller,
			'action' => $action,
			'pass' => $params,
		));

		// Grab the controller, this may have to create it :(
		$controllerObj = $this->__getController($controller);
		// Have to call beforeFilter to set-up the auth properly
		$controllerObj->beforeFilter();

		// First we need to check if the user must be logged in to do this action
		$allowedActions = $controllerObj->Auth->allowedActions;
		$isAllowed = (
			$allowedActions == array('*') || # AT [29/09/2012] Allow all actions?
			in_array($action, array_map('strtolower', $allowedActions))
		);

		if ($isAllowed) {
			return true;
		}

		$user = AuthComponent::user();
		return $controllerObj->Auth->isAuthorized($user, $request);
	}

/**
 * Given the name of a controller, return an instance of said controller
 * in the fastest method possible.
 * @param  string $name The name of the controller.
 * @return Controller|null A controller instance if we can find or create one, null otherwise.
 */
	private function __getController($name) {
		// First check to see if we have a cached copy of this controller
		if (array_key_exists($name, $this->__controllers)) {
			return $this->__controllers[$name];
		} else {
			// Are we lucky enough to be checking isAuthorized on the
			// controller we're currently attached to?
			$controllerToUse = null;
			$controllerName = '';
			if ($this->__currentController != null &&
				$this->__currentController->name == $name
			) {
				// Awesome, this is not too slow
				$controllerToUse = $this->__currentController;
				$controllerName = $name;
			}

			if ($controllerToUse == null) {
				// Nope, we are left to construct the controller and everything
				$__controllers = App::objects('controller');
				foreach ($__controllers as $controllerClassName) {
					$controllerName = str_replace('Controller', '', $controllerClassName);
					if (strtolower($controllerName) == strtolower($name)) {
						App::import('Controller', $controllerName);
						$controllerToUse = new $controllerClassName;

						$collection = new ComponentCollection();
						$collection->init($controllerToUse);
						$controllerToUse->Auth = new AuthComponent($collection);

						break;
					}
				}
			}

			if ($controllerToUse != null) {
				$this->__controllers[$controllerName] = $controllerToUse;
				return $controllerToUse;
			}
		}
		return null;
	}
}
