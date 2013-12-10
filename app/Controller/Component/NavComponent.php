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
 * NavComponent exists to check if a navigation link is
 * allowed (by checking if the current user is authorized to perform it), 
 * allowed links are added to a list which can then be rendered in the view.
 */
class NavComponent extends Component {

/**
 * The list of components this component relies on.
 * @var array
 */
	public $components = array( 'AuthUtil' );

/**
 * List of allowed actions.
 * @var array
 */
	private $__allowedActions = array();

/**
 * Add a link to an external site
 * @param string $text The display-text for the link.
 * @param string $url The URL of the link.
 * @param string $class The class the link should be rendered with.
 */
	public function addExternal($text, $url, $class = '') {
		array_push($this->__allowedActions, array( 'text' => $text, 'url' => $url, 'class' => $class ) );
	}

/**
 * Attempt to add a link, will only be added if the user is authorized
 * to perform the action the URL is pointing at.
 * @param string $text The display-text for the link.
 * @param string $controller The name of the controller the link refers to.
 * @param string $action The action the link refers to.
 * @param array  $params The params to be passed to the action.
 * @param string $class The class the link should be rendered with.
 */
	public function add($text, $controller, $action, $params = array(), $class = '') {
		if ($this->AuthUtil->isAuthorized($controller, $action, $params)) {
			array_push($this->__allowedActions, array( 'text' => $text, 'controller' => $controller, 'action' => $action, 'params' => $params, 'class' => $class ) );
		}
	}

/**
 * Get the list of allowed actions
 * @return array The list of allowed actions for the current user.
 */
	public function getAllowedActions() {
		return $this->__allowedActions;
	}

}