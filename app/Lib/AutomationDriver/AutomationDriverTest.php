<?php

	require_once('AutomationDriver.php');

	class AutomationDriverTest extends CakeTestCase
	{
		protected $automationDriver;

		public function setUp()
		{
			parent::setUp();
			$this->automationDriver = new AutomationDriver();
		}
	}

?>