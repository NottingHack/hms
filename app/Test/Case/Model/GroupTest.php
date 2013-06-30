<?php

    App::uses('Pin', 'Model');

    class GroupTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Group', 'app.Member', 'app.GroupsMember', 'app.Permission', 'app.GroupPermission' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Group = ClassRegistry::init('Group');
        }

        public function testGetDescription()
        {
            $this->assertIdentical( Hash::get($this->Group->getDescription(1), 'Group.grp_description'), 'Full Access', 'Returned incorrect title for group 1.' );
            $this->assertEqual( $this->Group->getDescription(0), array(), 'Returned incorrect title for group 0.' );
            $this->assertEqual( $this->Group->getDescription(null), array(), 'Returned incorrect title for group null.' );
        }
    }

?>