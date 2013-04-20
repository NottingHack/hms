<?php

    App::uses('Pin', 'Model');

    class MemberEmailTest extends CakeTestCase 
    {
        public function setUp() 
        {
        	parent::setUp();
            $this->MemberEmail = ClassRegistry::init('MemberEmail');
        }

        public function testGetMessage()
        {
            $this->assertEqual( $this->MemberEmail->getMessage(null), null, 'Null data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(0), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(-1), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(1), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage('foo'), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(array()), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(array('MemberEmail')), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(array('Member')), null, 'Invalid data was not handled correctly.' );
            $this->assertEqual( $this->MemberEmail->getMessage(array('Member' => array('message' => 'foooooooo'))), null, 'Invalid data was not handled correctly.' );


            $this->assertEqual( $this->MemberEmail->getMessage(array('MemberEmail' => array('message' => 'Lorem ipsum'))), 'Lorem ipsum', 'Valid data was not handled correctly.' );
        }
    }

?>