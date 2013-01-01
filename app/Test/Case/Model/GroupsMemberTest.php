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
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 1), true, 'Member 1 is not in group 1.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 2), true, 'Member 1 is not in group 2.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 2), true, 'Member 2 is not in group 2.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 2), true, 'Member 3 is not in group 2.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 2), true, 'Member 4 is not in group 2.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 2), true, 'Member 5 is not in group 2.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 3), true, 'Member 2 is not in group 3.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 4), true, 'Member 4 is not in group 4.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 5), true, 'Member 5 is not in group 5.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(null, 1), false, 'Member null is in group 1.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 3), false, 'Member 1 is in group 3.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 4), false, 'Member 1 is in group 4.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 1), false, 'Member 2 is in group 1.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 4), false, 'Member 2 is in group 4.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 5), false, 'Member 2 is in group 5.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 1), false, 'Member 3 is in group 1.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 3), false, 'Member 3 is in group 3.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 4), false, 'Member 3 is in group 4.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 5), false, 'Member 3 is in group 5.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 1), false, 'Member 4 is in group 1.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 3), false, 'Member 4 is in group 3.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 5), false, 'Member 4 is in group 5.' );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 1), false, 'Member 5 is in group 1.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 3), false, 'Member 5 is in group 3.' );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 4), false, 'Member 5 is in group 4.' );
        }
    }

?>