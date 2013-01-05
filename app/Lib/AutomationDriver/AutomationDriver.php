<?php

	require_once('phpwebdriver/__init__.php');

	App::uses('PhpReader', 'Configure');
	App::uses('Router', 'Routing');
	App::uses('CakeRequest', 'Network');
	
	//! A wrapper around Selenium to communicate with HMS
	class AutomationDriver
	{

		private $rootUrl; //!< URL to the root of HMS.
		private $webDriver; //!< Seleium driver.
		private $session; //!< Seleium session.

		public function __construct()
		{
			Configure::config('default', new PhpReader());
			Configure::load('automation', 'default');
			//Configure::load('routes', 'default');

			$phpSelf = $_SERVER['PHP_SELF'];
			$selfParts = explode('/', $phpSelf);
			$this->rootUrl = '';
			$partIndex = 0;
			$firstPart = true;
			while (	$selfParts[$partIndex] != 'app' &&
					$partIndex < count($selfParts)) 
			{
				if($selfParts[$partIndex] != '')
				{
					if(!$firstPart)
					{
						$this->rootUrl .= '/';	
					}
					$firstPart = false;
					$this->rootUrl .= $selfParts[$partIndex];
				}
				$partIndex++;
			}
			$this->webDriver = new PHPWebDriver_WebDriver(Configure::read('automation_driver_selenium_url'));

			new CakeRequest();
		}


		public function connect()
		{
			$this->session = $this->webDriver->session('firefox');
		}

		public function disconnect()
		{
			$this->session->close();
		}

		public function navigateToUrl($url)
		{
			$this->session->open($url);
			return true;
		}

		public function navigateToHomePage()
		{
			return $this->_navigteToControllerAction('pages', 'home');
		}

		public function navigateToMemberRegister()
		{
			return $this->_navigteToControllerAction('members', 'register');
		}

		public function pageHasNoErrors()
		{
			$elements = $this->session->elements('class name', 'cake-error');
			return true;//empty($elements);
		}

		public function getElementById($id)
		{
			return $this->session->element(PHPWebDriver_WebDriverBy::ID, $id);
		}

		public function getElementByClassName($className)
		{
			return $this->session->element(PHPWebDriver_WebDriverBy::CLASS_NAME, $className);
		}

		private function _navigteToControllerAction($controller, $action, $params = array())
		{
			$routeingArray = $params;
			$routeingArray['controller'] = $controller;
			$routeingArray['action'] = $action;

			$url = Router::url('/', true) . $this->rootUrl . Router::url($routeingArray, false);
			return $this->navigateToUrl($url);
		}
	}

?>