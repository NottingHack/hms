<?php

    App::uses('Status', 'Model');

    class StatusTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Status', 'app.Member', 'app.Account', 'app.Pin', 'app.Group', 'app.GroupsMember', 'app.StatusUpdate' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Status = ClassRegistry::init('Status');
        }

        public function testGetStatusSummaryAll()
        {
            $statusList = $this->Status->getStatusSummaryAll();

            $this->assertIdentical( count($statusList), $this->Status->find('count'), 'All statuses not included.' );
            $this->assertInternalType( 'array', $statusList, 'statusList is not an array.' );

            foreach ($statusList as $statusInfo)
            {
                $this->assertArrayHasKey( 'id', $statusInfo, 'Status has no id.' ); 
                $this->assertGreaterThan( 0, $statusInfo['id'], 'Status id is invalid.' );

                $this->assertArrayHasKey( 'name', $statusInfo, 'Status has no name.' ); 
                $this->assertArrayHasKey( 'count', $statusInfo, 'Status has no count.' ); 

                $expectedMemberCount = $this->Status->Member->find('count', array('conditions' => array('Member.member_status' => $statusInfo['id'])));
                $this->assertIdentical( $statusInfo['count'], $expectedMemberCount, 'Status count is incorrect for id: ' . $statusInfo['id'] . '.' );
            }
        }

        public function testGetStatusSummaryForId()
        {
            $this->assertIdentical( count($this->Status->getStatusSummaryForId(0)), 0, 'Status 0 returned some results.' );
            $this->assertIdentical( count($this->Status->getStatusSummaryForId(7)), 0, 'Status 7 returned some results.' );

            $statusList = $this->Status->getStatusSummaryForId(1);

            $this->assertInternalType( 'array', $statusList, 'statusList is not an array.' );

            $this->assertArrayHasKey( 'id', $statusList, 'Status has no id.' ); 
            $this->assertGreaterThan( 0, $statusList['id'], 'Status id is invalid.' );

            $this->assertArrayHasKey( 'name', $statusList, 'Status has no name.' ); 
            $this->assertArrayHasKey( 'count', $statusList, 'Status has no count.' ); 

            $expectedMemberCount = $this->Status->Member->find('count', array('conditions' => array('Member.member_status' => $statusList['id'])));
            $this->assertIdentical( $statusList['count'], $expectedMemberCount, 'Status count is incorrect for id: ' . $statusList['id'] . '.' );
        }
    }

?>