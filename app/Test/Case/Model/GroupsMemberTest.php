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
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 1), true );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 2), true );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 2), true );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 2), true );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 2), true );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 2), true );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 3), true );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 4), true );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 5), true );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(null, 1), false );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 3), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(1, 4), false );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 1), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 4), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(2, 5), false );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 1), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 3), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 4), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(3, 5), false );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 1), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 3), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(4, 5), false );

            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 1), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 3), false );
            $this->assertIdentical( $this->GroupsMember->isMemberInGroup(5, 4), false );
        }
    }

?>