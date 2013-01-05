<?php

    App::uses('HmsAutomationDriverTest', 'Lib/AutomationDriver');

    class UserCanRegisterTest extends HmsAutomationDriverTest 
    {
        public function setUp() 
        {
        	parent::setUp();
            $this->automationDriver->connect();
        }

        public function testCanRegister()
        {
            $this->reigsterNewMember('test001.pyroka@gmail.com');
        }

        public function tearDown()
        {
        	//$this->automationDriver->disconnect();
        }
    }

?>