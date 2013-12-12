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

require_once ('AutomationDriver.php');

/**
 * A base class for CakePHP tests that use AutomationDriver.
 */
class AutomationDriverTest extends CakeTestCase {

/**
 * The AutomationDriver for this test.
 * @var AutomationDriver
 */
	protected $_automationDriver;

/**
 * Create the AutomationDriver.
 */
	public function setUp() {
		parent::setUp();
		$this->_automationDriver = new AutomationDriver();
	}
}