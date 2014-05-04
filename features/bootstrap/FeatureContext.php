<?php

require_once ('HmsContext.php');

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\TranslatedContextInterface;
use Behat\Behat\Event\ScenarioEvent;
use Behat\Behat\Event\StepEvent;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends HmsContext {

/**
 * Initializes context.
 * Every scenario gets it's own context object.
 *
 * @param array $parameters context parameters (set them up through behat.yml)
 */
	public function __construct(array $parameters) {
		$testDir = sys_get_temp_dir() . '/HMS_Test/' . date("Y-m-d-H-i-s") . '/';

		$subcontextParams = array(
			'testDir' => $testDir,
			'logger' => new KLogger($testDir, KLogger::INFO),
		);

		$mergedParams = array_merge($parameters, $subcontextParams);
		parent::__construct($mergedParams);

		// Initialize your context here
		$this->useContext('member', new MemberContext($mergedParams));
		$this->useContext('config', new ConfigContext($mergedParams));
		$this->useContext('navigation', new NavigationContext($mergedParams));
		$this->useContext('file', new FileContext($mergedParams));
		$this->useContext('email', new EmailContext($mergedParams));
		$this->useContext('driver', new DriverContext($mergedParams));
	}

/**
 * @BeforeScenario
 */
	public function beforeScenario(ScenarioEvent $event) {
		$this->_logger()->logInfo('<<< Begin Scenario: ' . $event->getScenario()->getTitle() . ' >>>');
		$this->_configContext()->beforeScenario();
		$this->_emailContext()->beforeScenario();
	}

/**
 * @AfterScenario
 */
	public function afterScenario(ScenarioEvent $event) {
		$this->_logger()->logInfo('<<< End Scenario: ' . $event->getScenario()->getTitle() . ' >>>');
	}

/**
 * @BeforeStep
 */
	public function beforeStep(StepEvent $event) {
		$this->_logger()->logInfo('<< Begin Step: ' . $event->getStep()->getText() . ' >>');
	}

/**
 * @AfterStep
 */
	public function afterStep(StepEvent $event) {
		$this->_logger()->logInfo('<< End Step: ' . $event->getStep()->getText() . ' >>');
	}
}
