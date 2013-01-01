<?php

    App::uses('GroupsMember', 'Model');

    class GroupsMemberTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.GroupsMember', 'app.member', 'app.Status', 'app.Group' );

        public function setUp() 
        {
        	parent::setUp();
            $this->GroupsMember = ClassRegistry::init('GroupsMember');
        }

        public function testIsInGroup()
        {
            $this->assertTrue( $this->GroupsMember->isMemberInGroup(1, 1), 'Member 1 is not in group 1.' );

            $this->assertTrue( $this->GroupsMember->isMemberInGroup(1, 2), 'Member 1 is not in group 2.' );
            $this->assertTrue( $this->GroupsMember->isMemberInGroup(2, 2), 'Member 2 is not in group 2.' );
            $this->assertTrue( $this->GroupsMember->isMemberInGroup(3, 2), 'Member 3 is not in group 2.' );
            $this->assertTrue( $this->GroupsMember->isMemberInGroup(4, 2), 'Member 4 is not in group 2.' );
            $this->assertTrue( $this->GroupsMember->isMemberInGroup(5, 2), 'Member 5 is not in group 2.' );

            $this->assertTrue( $this->GroupsMember->isMemberInGroup(2, 3), 'Member 2 is not in group 3.' );

            $this->assertTrue( $this->GroupsMember->isMemberInGroup(4, 4), 'Member 4 is not in group 4.' );

            $this->assertTrue( $this->GroupsMember->isMemberInGroup(5, 5), 'Member 5 is not in group 5.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(null, 1), 'Member null is in group 1.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(1, 3), 'Member 1 is in group 3.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(1, 4), 'Member 1 is in group 4.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(2, 1), 'Member 2 is in group 1.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(2, 4), 'Member 2 is in group 4.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(2, 5), 'Member 2 is in group 5.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(3, 1), 'Member 3 is in group 1.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(3, 3), 'Member 3 is in group 3.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(3, 4), 'Member 3 is in group 4.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(3, 5), 'Member 3 is in group 5.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(4, 1), 'Member 4 is in group 1.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(4, 3), 'Member 4 is in group 3.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(4, 5), 'Member 4 is in group 5.' );

            $this->assertFalse( $this->GroupsMember->isMemberInGroup(5, 1), 'Member 5 is in group 1.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(5, 3), 'Member 5 is in group 3.' );
            $this->assertFalse( $this->GroupsMember->isMemberInGroup(5, 4), 'Member 5 is in group 4.' );
        }
    }

?>