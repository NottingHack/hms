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
 * @package       app.Lib.AutomationDriver
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

require_once ('phpwebdriver/__init__.php');

App::uses('PhpReader', 'Configure');
App::uses('Router', 'Routing');
App::uses('CakeRequest', 'Network');

/**
 * A wrapper around Selenium to communicate with HMS.
 */
class AutomationDriver {

/**
 * URL to the root of HMS.
 * @var string
 */
	private $__rootUrl;

/**
 * Selenium driver.
 * @var PHPWebDriver_WebDriver
 */
	private $__webDriver;

/**
 * Selenium session.
 * @var PHPWebDriver_WebDriverSession
 */
	private $__session;

/**
 * Constructor.
 */
	public function __construct() {
		Configure::config('default', new PhpReader());
		Configure::load('automation', 'default');
		//Configure::load('routes', 'default');

		$phpSelf = $_SERVER['PHP_SELF'];
		$selfParts = explode('/', $phpSelf);
		$this->__rootUrl = '';
		$partIndex = 0;
		$firstPart = true;
		$numParts = count($selfParts);
		while ($selfParts[$partIndex] != 'app' && $partIndex < $numParts) {
			if ($selfParts[$partIndex] != '') {
				if (!$firstPart) {
					$this->__rootUrl .= '/';
				}
				$firstPart = false;
				$this->__rootUrl .= $selfParts[$partIndex];
			}
			$partIndex++;
		}
		$this->__webDriver = new PHPWebDriver_WebDriver(Configure::read('automation_driver_selenium_url'));

		new CakeRequest();
	}

/**
 * Create a session and connect to it.
 */
	public function connect() {
		$this->__session = $this->__webDriver->__session('firefox');
	}

/**
 * Disconnect and close a session.
 */
	public function disconnect() {
		$this->__session->close();
	}

/**
 * Navigate to a URL.
 * @param  string $url The URL to navigate to.
 * @return bool True if navigation is sucessful, false otherwise.
 */
	public function navigateToUrl($url) {
		$this->__session->open($url);
		return true;
	}

/**
 * Navigate to the home page.
 * @return bool True if navigation is sucessful, false otherwise.
 */
	public function navigateToHomePage() {
		return $this->__navigteToControllerAction('pages', 'home');
	}

/**
 * Navigate to the member register page.
 * @return bool True if navigation is sucessful, false otherwise.
 */
	public function navigateToMemberRegister() {
		return $this->__navigteToControllerAction('members', 'register');
	}

/**
 * Check to see if the currently loaded page has no errors.
 * @return bool True if there are no errors, false otherwise.
 */
	public function pageHasNoErrors() {
		$elements = $this->__session->elements('class name', 'cake-error');
		return true;
	}

/**
 * Get an element onthe currently loaded page by its ID.
 * @param  string $id The name of the ID to look for.
 * @return PHPWebDriver_WebDriverElement|null The element if it can be found, null otherwise.
 */
	public function getElementById($id) {
		return $this->__session->element(PHPWebDriver_WebDriverBy::ID, $id);
	}

/**
 * Get an element onthe currently loaded page by its class.
 * @param  string $className The name of the class to look for.
 * @return PHPWebDriver_WebDriverElement|null The element if it can be found, null otherwise.
 */
	public function getElementByClassName($className) {
		return $this->__session->element(PHPWebDriver_WebDriverBy::CLASS_NAME, $className);
	}

/**
 * Navigate to a CakePHP controller action.
 * @param  string $controller Name of the controller.
 * @param  string $action Name of the action.
 * @param  array  $params Parameters for the action.
 * @return bool True if navigation was sucessful, false otherwise.
 */
	private function __navigteToControllerAction($controller, $action, $params = array()) {
		$routeingArray = $params;
		$routeingArray['controller'] = $controller;
		$routeingArray['action'] = $action;

		$url = Router::url('/', true) . $this->__rootUrl . Router::url($routeingArray, false);
		return $this->navigateToUrl($url);
	}
}