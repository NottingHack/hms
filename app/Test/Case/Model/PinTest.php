<?php

    App::uses('Pin', 'Model');

    class PinTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Pin', 'app.Member' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Pin = ClassRegistry::init('Pin');
        }

        public function testGeneratePin()
        {
            $pin = $this->Pin->generatePin();

            $this->assertLessThanOrEqual(9999, $pin);
            $this->assertGreaterThanOrEqual(1000, $pin);
        }

        public function testGenerateUniquePin()
        {
            $pin = $this->Pin->generateUniquePin();

            $this->assertLessThanOrEqual(9999, $pin);
            $this->assertGreaterThanOrEqual(1000, $pin);

            $this->assertIdentical( $this->Pin->find('count', array('conditions' => array('Pin.pin' => '7422'))), 1 );
            $this->assertIdentical( $this->Pin->find('count', array('conditions' => array('Pin.pin' => $pin))), 0 );
        }
    }

?>