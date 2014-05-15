<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\Then;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Event\ScenarioEvent;

require_once ('KLogger.php');
require_once ('PHPUnit/Autoload.php');
require_once ('PHPUnit/Framework/Assert/Functions.php');

class HmsContext extends BehatContext {

	private $__testDir;

	private $__logger;

	public function __construct(array $parameters) {
		$this->__testDir = $parameters['testDir'];
		$this->__logger = $parameters['logger'];
	}

	private function __getSubContext($name) {
		return $this->getMainContext()->getSubcontext($name);
	}

	protected function _configContext() {
		return $this->__getSubContext('config');
	}

	protected function _emailContext() {
		return $this->__getSubContext('email');
	}

	protected function _fileContext() {
		return $this->__getSubContext('file');
	}

	protected function _memberContext() {
		return $this->__getSubContext('member');
	}

	protected function _navigationContext() {
		return $this->__getSubContext('navigation');
	}

	protected function _databaseContext() {
		return $this->__getSubContext('database');
	}

	protected function _testDir() {
		return $this->__testDir;
	}

	protected function _logger() {
		return $this->__logger;
	}

	protected function _fail($message) {
		$this->getMainContext()->fail($message);
	}
}