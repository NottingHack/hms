<?php

    App::uses('AutomationDriverTest', 'Lib/AutomationDriver');

    class UserCanRegisterTest extends AutomationDriverTest 
    {
        public function setUp() 
        {
        	parent::setUp();
            $this->automationDriver->connect();
        }

        public function testCanRegister()
        {
            $this->automationDriver->navigateToMemberRegister();
            $this->assertTrue( $this->automationDriver->pageHasNoErrors(), 'Registration page has errors.' );
        }

        public function tearDown()
        {
        	$this->automationDriver->disconnect();
        }
    }

?>